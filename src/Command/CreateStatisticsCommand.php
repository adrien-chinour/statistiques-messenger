<?php

namespace App\Command;

use App\Core\Entity\Conversation;
use App\Core\Module\ModuleFactory;
use App\Core\Renderer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CreateStatisticsCommand extends Command
{

    private static string $name = 'app:statistics:create';

    private EntityManager $manager;

    private ModuleFactory $factory;

    private Renderer $renderer;

    public function __construct(EntityManager $manager, ModuleFactory $factory, Renderer $renderer, ?string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
        $this->factory = $factory;
        $this->renderer = $renderer;
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setAliases(['a:s:c']);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $question = new Question('Conversation to load', null);
        $question->setAutocompleterValues($this->manager->getRepository(Conversation::class)->findAll());
        $conversationName = $io->askQuestion($question);
        if ($conversationName === null) {
            return 1;
        }

        /** @var Conversation|null $conversation */
        $conversation = $this->manager->getRepository(Conversation::class)->findOneBy(['name' => $conversationName]);
        if ($conversation === null) {
            return 1;
        }

        // write output
        $content = "";
        foreach ($this->factory->loadModules() as $module) {
            $content .= $module->build($conversation);
        }

        try {
            $output = "output/conversations/$conversationName.html";
            $this->renderer->output('conversation.html.twig', $output, ["content" => $content]);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }

        return 0;
    }

}
