<?php

namespace App\Command;

use App\Core\DataFolderReader;
use App\Core\Entity\Conversation;
use App\Core\Entity\Message;
use App\Core\Entity\Person;
use App\Core\Entity\Reaction;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ImportConversationCommand extends Command
{
    const PROGRESS_BAR_FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

    private static string $name = 'conversation:import';

    private DataFolderReader $folderReader;

    private EntityManager $manager;

    private SymfonyStyle $io;

    public function __construct(DataFolderReader $folderReader, EntityManager $manager, $name = null)
    {
        parent::__construct($name);
        $this->folderReader = $folderReader;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this->setName(self::$name);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title("Import data from conversation");

        $this->io->section("Settings");
        $conversationFolder = $this->getConversationFolder();
        $conversationName = $this->getConversationName();

        $this->io->section("Importing data (this may be long)");
        $conversation = $this->importConversation($conversationFolder, $conversationName);

        $messages = [];
        $persons = [];
        foreach ($this->folderReader->getConversationFiles($conversationFolder) as $file) {
            $json = json_decode(file_get_contents("$conversationFolder/$file"), true);
            $messages[] = $json["messages"];
            $persons[] = $json["participants"];
        }

        $this->importPersons($conversation, $persons);
        $this->importMessages($conversation, $messages);

        $this->io->success("Conversation correctly imported.");
        return 0;
    }

    private function getConversationFolder(): string
    {
        $question = new Question('Conversation to load', null);
        $question->setAutocompleterValues($this->folderReader->getMessageFolders());
        $conversationFolder = $this->io->askQuestion($question);
        return $this->folderReader->getConversationFolder($conversationFolder);
    }

    private function getConversationName(): string
    {
        return $this->io->ask("Name this conversation import");
    }

    /**
     * @param string $folder
     * @param string $name
     * @return Conversation
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function importConversation(string $folder, string $name): Conversation
    {
        $conversation = new Conversation();
        $conversation
            ->setName($name)
            ->setUuid(md5(uniqid()))
            ->setFolder($folder);
        $this->manager->persist($conversation);
        $this->manager->flush();

        return $conversation;
    }

    /**
     * @param Conversation $conversation
     * @param array $persons
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function importPersons(Conversation $conversation, array $persons): void
    {
        $pb = $this->io->createProgressBar($this->getChunkSize($persons));
        $pb->setFormat(self::PROGRESS_BAR_FORMAT);

        $pb->start();
        foreach ($persons as $person) {
            $name = $person["name"];
            $exist = $this->manager->getRepository(Person::class)->findOneBy(['conversation' => $conversation, 'name' => $name]);

            if ($exist === null) {
                $entity = new Person();
                $entity
                    ->setName($name)
                    ->setConversation($conversation);

                $this->manager->persist($entity);
                $this->manager->flush();
            }
            $pb->advance();
        }
        $pb->finish();
    }

    /**
     * @param Conversation $conversation
     * @param array $messages
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function importMessages(Conversation $conversation, array $messages): void
    {
        $persons = $this->manager->getRepository(Person::class)->findBy(['conversation' => $conversation]);

        $pb = $this->io->createProgressBar($this->getChunkSize($messages));
        $pb->setFormat(self::PROGRESS_BAR_FORMAT);

        $pb->start();
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

        $chunk = 0;
        foreach ($messages as $message) {
            $chunk += 1;
            $name = $message["sender_name"];
            $person = $this->getAuthor($persons, $name);

            if ($person === null) {
                $person = new Person();
                $person
                    ->setName($name)
                    ->setConversation($conversation);
                $this->manager->persist($person);
                $this->manager->flush();
                $persons[] = $person;
            }

            if (isset($message["content"]) && $message["type"] == "Generic" && $message["content"] !== null) {
                $timestamp = round($message['timestamp_ms'] / 1000);

                $entity = new Message();
                $entity
                    ->setConversation($conversation)
                    ->setContent($message["content"])
                    ->setAuthor($person)
                    ->setDatetime(new \DateTime("@$timestamp"));
                $this->manager->persist($entity);

                if (isset($message["reactions"])) {
                    $this->importReactions($message['reactions'], $persons, $entity);
                }
            }

            if ($chunk % 100 === 0) {
                $this->manager->flush();
                $this->manager->clear(Message::class);
                $this->manager->clear(Reaction::class);
            }

            $pb->advance();
        }

        $this->manager->flush();
        $pb->finish();
    }

    /**
     * @param array $reactions
     * @param array $persons
     * @param Message $message
     * @throws ORMException
     */
    private function importReactions(array $reactions, array $persons, Message $message)
    {
        foreach ($reactions as $reaction) {

            $actor = $this->getAuthor($persons, $reaction['actor']);
            if ($actor === null) {
                $actor = (new Person())
                    ->setName($reaction['actor'])
                    ->setConversation($message->getConversation());
                $this->manager->persist($actor);
            }

            $entity = (new Reaction())
                ->setAuthor($actor)
                ->setContent($reaction["reaction"])
                ->setMessage($message);
            $this->manager->persist($entity);
        }
    }

    private function getAuthor(array $persons, string $name): ?Person
    {
        /** @var Person $person */
        foreach ($persons as $person) {
            if ($person->getName() === $name) {
                return $person;
            }
        }

        return null;
    }

    private function getChunkSize(array $chunks): int
    {
        return array_reduce($chunks, function ($size, $chunk) {
            return $size + count($chunk);
        });
    }

}
