<?php

namespace App\Command;

use App\Core\Entity\Conversation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConversationCleanCommand extends Command
{
    private static string $name = 'conversation:clean';

    private SymfonyStyle $io;

    private EntityManager $manager;

    public function __construct(EntityManager $manager, string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this->setName(self::$name);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io->confirm("This action will remove all imported conversation in database. Continue ?");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->manager->getConnection();
        foreach (['reaction', 'message', 'person', 'conversation'] as $table) {
            $connection->exec("delete from $table");
        }
        $this->io->success("All conversations removed.");

        return 0;
    }
}
