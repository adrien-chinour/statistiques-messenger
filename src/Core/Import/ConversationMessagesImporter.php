<?php


namespace App\Core\Import;


use App\Core\Entity\Media;
use App\Core\Entity\Message;
use App\Core\Entity\Person;
use App\Core\Entity\Reaction;
use App\Core\Import\Serializer\Object\MediaSerializer;
use App\Core\Import\Serializer\Object\MessageSerializer;
use App\Core\Import\Serializer\Object\ReactionSerializer;
use Doctrine\ORM\EntityManager;

class ConversationMessagesImporter
{
    private EntityManager $manager;

    private MessageSerializer $serializer;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->serializer = new MessageSerializer(new MediaSerializer(), new ReactionSerializer());
    }

    public function __invoke(ConversationContext $context, array $messages): \Generator
    {
        $this->manager->getConnection()->getConfiguration()->setSQLLogger();

        $chunk = 0;
        $persons = [];
        foreach ($messages as $normalizedMessage) {
            foreach ($this->getActors($normalizedMessage) as $actor) {
                if (!in_array($actor, $persons)) {
                    $newPerson = (new Person())->setName($actor)->setConversation($context->conversation());
                    $context->conversation()->addPerson($newPerson);
                    $this->manager->persist($newPerson);
                    $persons[] = $actor;
                }
            }

            if ($normalizedMessage["type"] === "Generic") {
                $message = $this->serializer->denormalize($normalizedMessage, $context);
                $this->manager->persist($message);
                $chunk += 1;
            }

            if ($chunk % 100 === 0) {
                $this->manager->flush();
                $this->manager->clear(Message::class);
                $this->manager->clear(Reaction::class);
                $this->manager->clear(Media::class);
            }

            yield;
        }
    }

    private function getActors(array $normalizedMessage): array
    {
        $actors = [];
        if (isset($normalizedMessage['reactions'])) {
            $actors = array_map(fn($reaction) => $reaction['actor'], $normalizedMessage['reactions']);
        }
        array_push($actors, $normalizedMessage['sender_name']);

        return array_unique($actors);
    }
}
