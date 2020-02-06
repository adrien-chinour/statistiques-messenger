<?php

namespace App\Command;

use App\Entity\Conversation;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\Renderer;

class CreateStatisticsCommand extends Command
{

    private static $name = 'app:statistics:create';

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var PersonRepository
     */
    private $personRepository;
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    public function __construct(EntityManager $manager,
                                Renderer $renderer,
                                PersonRepository $personRepository,
                                MessageRepository $messageRepository,
                                ?string $name = null)
    {
        parent::__construct($name);
        $this->manager = $manager;
        $this->renderer = $renderer;
        $this->personRepository = $personRepository;
        $this->messageRepository = $messageRepository;
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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

        $options = [
            'persons' => $this->personRepository->getClassement($conversation),
            'conversation' => $conversation,
            'total' => $this->messageRepository->countMessages($conversation),
            'ratio' => $this->messageRepository->ratio($conversation),
            'firstMessage' => $this->messageRepository->getFirstMessage($conversation),
        ];

        $this->renderer->output(
            'conversation.html.twig',
            "output/conversations/$conversationName.html",
            $options
        );

        return 0;
    }

}
