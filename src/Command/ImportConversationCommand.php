<?php

namespace App\Command;

use App\Entity\Conversation;
use App\Entity\Person;
use App\Service\DataFolderReader;
use App\Service\EntityConverter;
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

    private static string $name = 'app:import:conversation';

    private DataFolderReader $folderReader;

    private EntityConverter $converter;

    private EntityManager $manager;

    private SymfonyStyle $io;

    public function __construct(DataFolderReader $folderReader, EntityConverter $converter, EntityManager $manager, $name = null)
    {
        parent::__construct($name);
        $this->folderReader = $folderReader;
        $this->converter = $converter;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setAliases(['a:i:c']);
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
        return $this->converter->importConversation($folder, $name);
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
        foreach ($persons as $chunk) {
            foreach ($this->converter->importPersons($conversation, $chunk) as $line) {
                $pb->advance();
            }
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
        foreach ($messages as $chunk) {
            foreach ($this->converter->importMessages($conversation, $persons, $chunk) as $line) {
                $pb->advance();
            }
        }
        $pb->finish();
    }

    private function getChunkSize(array $chunks): int
    {
        return array_reduce($chunks, function ($size, $chunk) {
            return $size + count($chunk);
        });
    }

}
