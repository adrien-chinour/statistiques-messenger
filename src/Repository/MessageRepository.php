<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use Doctrine\ORM\EntityManager;

class MessageRepository
{

    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function countMessages(Conversation $conversation): int
    {
        $query = $this->manager->createQueryBuilder()
            ->select('count(m.id)')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();

        return $query->execute()[0][1];
    }

    public function getFirstMessage(Conversation $conversation)
    {
        $query = $this->manager->createQueryBuilder()
            ->select('m')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->orderBy('m.datetime')
            ->setMaxResults(1)
            ->getQuery();

        return $query->execute();
    }

    public function getStartDate(Conversation $conversation)
    {
        $query = $this->manager->createQueryBuilder()
            ->select('m.datetime')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->orderBy('m.datetime')
            ->setMaxResults(1)
            ->getQuery();

        return $query->execute()[0]['datetime'];
    }

    public function getEndDate(Conversation $conversation)
    {
        $query = $this->manager->createQueryBuilder()
            ->select('m.datetime')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->orderBy('m.datetime', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->execute()[0]['datetime'];
    }

    public function ratio(Conversation $conversation)
    {
        $diff = date_diff($this->getStartDate($conversation), $this->getEndDate($conversation));

        return $this->countMessages($conversation) / $diff->days;
    }

}
