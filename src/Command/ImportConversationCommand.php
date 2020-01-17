<?php

namespace App\Command;

use App\Service\DataFolderReader;
use App\Service\EntityConverter;
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

    public function __construct(DataFolderReader $folderReader, EntityConverter $converter, $name = null)
    {
        parent::__construct($name);
        $this->folderReader = $folderReader;
        $this->converter = $converter;
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

        $pb = $io->createProgressBar(count($files));
        $pb->start();
        foreach ($files as $file) {
            $json = json_decode(file_get_contents("$absoluteFolder/$file"), true);
            $this->converter->importPersons($conversation, $json["participants"]);
            $this->converter->importMessages($conversation, $json["messages"]);
            $pb->advance();
        }
        $pb->finish();
        $io->success("Conversation correctly imported.");
        return 0;
    }


}
