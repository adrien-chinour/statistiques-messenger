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

final class ConversationStatisticsCommand extends Command
{

    private static string $name = 'conversation:stat';

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
        $this->setName(self::$name);
        $this->setAliases(['c:s']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $question = new Question('Conversation to load', null);
        $question->setAutocompleterValues($this->manager->getRepository(Conversation::class)->findAll());

        $conversationName = $io->askQuestion($question);
        $conversation = $this->manager->getRepository(Conversation::class)->findOneBy(['name' => $conversationName]);
        if ($conversationName === null || !$conversation instanceof Conversation) {
            return 1;
        }

        try {
            $this->renderer->output(
                'conversation.html.twig',
                "output/conversations/$conversationName/index.html",
                ["content" => $this->loadModuleContent($conversation)]
            );
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return 1;
        }

        $io->success(sprintf("Conversation stats generated in %s", "output/conversations/$conversationName/index.html"));

        return 0;
    }

    private function loadModuleContent(Conversation $conversation): string
    {
        $content = "";
        foreach ($this->factory->loadModules() as $module) {
            $content .= $module->build($conversation);
        }

        return $content;
    }

}
