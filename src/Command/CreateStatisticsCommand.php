<?php


namespace App\Command;


use App\Entity\Conversation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateStatisticsCommand extends Command
{

    private static $name = 'app:statistics:create';
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager, ?string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setAliases(['a:s:c']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $question = new Question('Conversation to load', null);
        $question->setAutocompleterValues($this->manager->getRepository(Conversation::class)->findAll());
        $conversationFolder = $io->askQuestion($question);
        if ($conversationFolder === null) {
            return 1;
        }

        return 0;
    }

}
