<?php

namespace App\Command;

use App\Service\EntityConverter;
use App\Service\DataFolderReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDataCommand extends Command
{

    protected static $name = 'app:import:all';

    /** @var DataFolderReader $service */
    private $folderReader;

    /** @var EntityConverter $converter */
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
            ->setAliases(['a:i:a'])
            ->setDescription('Import Messenger data JSON in a local SQLite database. This database allow us to create statistics more simply.')
            ->setHelp('Just import data from data folder in a local SQLite database. Use this command at start.');
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

        if ($this->folderReader->checkFolderExist() === false) {
            $io->error('Folder not exist, please read the documentation for more informations.');
            return 1;
        }

        $folders = $this->folderReader->getMessageFolders();
        $pb = $io->createProgressBar(count($folders));
        $pb->start();

        foreach ($folders as $folder) {
            $absoluteFolder = $this->folderReader->getInboxFolder() . "/$folder";
            $files = array_diff(scandir($absoluteFolder), ['.', '..']);
            $conversation = $this->converter->importConversation($absoluteFolder);

            foreach ($files as $file) {
                $json = json_decode(file_get_contents("$absoluteFolder/$file"), true);
                $this->converter->importPersons($conversation, $json["participants"]);
                $this->converter->importMessages($conversation, $json["messages"]);
            }

            $pb->advance();
        }

        return 0;
    }

}
