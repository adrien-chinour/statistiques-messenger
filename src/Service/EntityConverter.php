<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Person;

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
     * @return Conversation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importConversation(string $folder): Conversation
    {
        $conversation = new Conversation();
        $conversation->setUuid(md5(uniqid()));
        $conversation->setFolder($folder);
        $this->manager->persist($conversation);
        $this->manager->flush();

        return $conversation;
    }

    public function importPersons(Conversation $conversation, array $persons)
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

        }
    }

    public function importMessages(Conversation $conversation, array $messages)
    {
        foreach ($messages as $message) {
            $name = $message["sender_name"];
            $person = $this->manager->getRepository(Person::class)->findOneBy(['conversation' => $conversation, 'name' => $name]);

            if ($person === null) {
                $person = new Person();
                $person
                    ->setName($name)
                    ->setConversation($conversation);
                $this->manager->persist($person);
                $this->manager->flush();
            }

            if (isset($message["content"]) && $message["type"] == "Generic" && $message["content"] !== null) {
                $entity = new Message();
                $entity
                    ->setConversation($conversation)
                    ->setContent($message["content"])
                    ->setAuthor($person)
                    ->setDatetime(new \DateTime("@{$message['timestamp_ms']}"));
                $this->manager->persist($entity);
                $this->manager->flush();
            }
        }
    }

}
