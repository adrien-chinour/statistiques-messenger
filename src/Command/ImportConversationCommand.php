<?php

namespace App\Command;

use App\Entity\Person;
use App\Service\DataFolderReader;
use App\Service\EntityConverter;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportConversationCommand extends Command
{
    private static $name = 'app:import:conversation';

    /**
     * @var DataFolderReader
     */
    private $folderReader;
    /**
     * @var EntityConverter
     */
    private $converter;
    /**
     * @var EntityManager
     */
    private $manager;

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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $question = new Question('Conversation to load', null);
        $question->setAutocompleterValues($this->folderReader->getMessageFolders());
        $conversationFolder = $io->askQuestion($question);
        if ($conversationFolder === null) {
            return 1;
        }

        $io->title("Start importing data form $conversationFolder");
        $absoluteFolder = $this->folderReader->getInboxFolder() . "/$conversationFolder";
        $files = array_diff(scandir($absoluteFolder), ['.', '..']);
        $conversation = $this->converter->importConversation($absoluteFolder);

        $messages = [];
        foreach ($files as $file) {
            $json = json_decode(file_get_contents("$absoluteFolder/$file"), true);
            $this->converter->importPersons($conversation, $json["participants"]);
            $messages[] = $json["messages"];
        }

        $persons = $this->manager->getRepository(Person::class)->findBy(['conversation' => $conversation]);

        $size = array_reduce($messages, function ($size, $chunk) {
            $size += count($chunk);
            return $size;
        });

        $pb = $io->createProgressBar($size);
        $pb->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%%');
        $pb->start();
        foreach ($messages as $chunk) {
            $data = $chunk;
            foreach ($this->converter->importMessages($conversation, $persons, $data) as $line) {
                $pb->advance();
            }
        }
        $pb->finish();
        $io->success("Conversation correctly imported.");
        return 0;
    }


}
