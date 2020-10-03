<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Person;
use Doctrine\ORM\EntityManager;

class PersonRepository
{

    private EntityManager $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function getRanking(Conversation $conversation)
    {
        $query = $this
            ->manager->createQueryBuilder()
            ->select('p.name, count(m.id) as nb_message')
            ->from(Person::class, 'p')
            ->where('p.conversation = :conversation_id')
            ->join(Message::class, 'm', 'WITH', 'p.id = m.author')
            ->groupBy('p.name')
            ->orderBy('nb_message', 'DESC')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();

        return $query->execute();
    }

}
