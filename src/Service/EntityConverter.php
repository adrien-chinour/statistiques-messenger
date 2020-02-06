<?php

namespace App\Service;

use App\Entity\Reaction;
use DateTime;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Person;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Generator;

/*
 * TODO split this  by entity
 */

class EntityConverter
{

    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $folder
     * @param string $name
     * @return Conversation
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function importConversation(string $folder, string $name): Conversation
    {
        $conversation = new Conversation();
        $conversation
            ->setName($name)
            ->setUuid(md5(uniqid()))
            ->setFolder($folder);
        $this->manager->persist($conversation);
        $this->manager->flush();

        return $conversation;
    }

    /**
     * @param Conversation $conversation
     * @param array $persons
     * @return Generator
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function importPersons(Conversation $conversation, array $persons): Generator
    {
        foreach ($persons as $person) {
            $name = $person["name"];
            $exist = $this->manager->getRepository(Person::class)->findOneBy(['conversation' => $conversation, 'name' => $name]);

            if ($exist === null) {
                $entity = new Person();
                $entity
                    ->setName($name)
                    ->setConversation($conversation);

                $this->manager->persist($entity);
                $this->manager->flush();
            }

            yield;
        }
    }

    /**
     * @param Conversation $conversation
     * @param array $persons
     * @param array $messages
     * @return Generator
     * @throws MappingException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function importMessages(Conversation $conversation, array $persons, array $messages): Generator
    {
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

        $chunk = 0;
        foreach ($messages as $message) {
            $chunk += 1;
            $name = $message["sender_name"];
            $person = $this->getAuthor($persons, $name);

            if ($person === null) {
                $person = new Person();
                $person
                    ->setName($name)
                    ->setConversation($conversation);
                $this->manager->persist($person);
                $this->manager->flush();
                $persons[] = $person;
            }

            if (isset($message["content"]) && $message["type"] == "Generic" && $message["content"] !== null) {
                $entity = new Message();
                $entity
                    ->setConversation($conversation)
                    ->setContent($message["content"])
                    ->setAuthor($person)
                    ->setDatetime(new DateTime("@{$message['timestamp_ms']}"));
                $this->manager->persist($entity);

                if (isset($message["reactions"])) {
                    $this->importReactions($message['reactions'], $persons, $entity);
                }
            }

            if ($chunk % 100 === 0) {
                $this->manager->flush();
                $this->manager->clear(Message::class);
                $this->manager->clear(Reaction::class);
            }
            yield;
        }

        $this->manager->flush();
    }

    /**
     * @param array $reactions
     * @param Message $message
     * @throws ORMException
     */
    private function importReactions(array $reactions, array $persons, Message $message)
    {
        foreach ($reactions as $reaction) {

            $entity = (new Reaction())
                ->setAuthor($this->getAuthor($persons, $reaction['actor']))
                ->setContent($reaction["reaction"])
                ->setMessage($message);
            $this->manager->persist($entity);
        }
    }

    private function getAuthor(array $persons, string $name): ?Person
    {
        /** @var Person $person */
        foreach ($persons as $person) {
            if ($person->getName() === $name) {
                return $person;
            }
        }

        return null;
    }
}
