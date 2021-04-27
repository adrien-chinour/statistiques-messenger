<?php

namespace App\Command;

use App\Core\DataFolderReader;
use App\Core\Entity\Conversation;
use App\Core\Entity\Media;
use App\Core\Entity\Message;
use App\Core\Entity\Reaction;
use App\Core\Import\ConversationContext;
use App\Core\Import\ConversationMessagesImporter;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConversationImportCommand extends Command
{
    const PROGRESS_BAR_FORMAT = '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';

    private static string $name = 'conversation:import';

    private DataFolderReader $folderReader;

    private EntityManager $manager;

    private SymfonyStyle $io;

    private ConversationMessagesImporter $messagesImporter;

    public function __construct(DataFolderReader $folderReader, EntityManager $manager, ConversationMessagesImporter $messagesImporter, $name = null)
    {
        parent::__construct($name);
        $this->folderReader = $folderReader;
        $this->manager = $manager;
        $this->messagesImporter = $messagesImporter;
    }

    protected function configure()
    {
        $this->setName(self::$name);
        $this->setAliases(['c:i']);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title("Import data from conversation");

        $this->manager->getConnection()->getConfiguration()->setSQLLogger();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->section("Settings");
        $question = (new Question('Conversation to load', null))->setAutocompleterValues($this->folderReader->getMessageFolders());
        $conversationFolder = $this->folderReader->getConversationFolder($this->io->askQuestion($question));
        $conversationName = $this->io->ask("Name this conversation import");

        $conversation = (new Conversation())->setName($conversationName)->setUuid(Uuid::uuid4()->toString())->setFolder($conversationFolder);
        $this->manager->persist($conversation);
        $this->manager->flush();

        $messages = [];
        foreach ($this->folderReader->getConversationFiles($conversationFolder) as $file) {
            $json = json_decode(file_get_contents("$conversationFolder/$file"), true);
            $messages = array_merge($messages, $json["messages"]);
        }

        $this->io->section("Importing data (this may be long)");

        $progressBar = $this->io->createProgressBar(count($messages));
        $progressBar->setFormat(self::PROGRESS_BAR_FORMAT);
        $progressBar->start();

        $context = new ConversationContext($conversation);
        foreach (($this->messagesImporter)($context, $messages) as $_) {
            $progressBar->advance();
        }
        $progressBar->finish();

        $this->io->newLine(2);
        $this->io->success("Conversation correctly imported.");

        return 0;
    }
}
