<?php

namespace App\Module;

use App\Core\Entity\Conversation;
use App\Core\Entity\Message;
use App\Core\Entity\Person;
use App\Core\Module\AbstractModule;

final class MostReactedMessageModule extends AbstractModule
{

    public int $weight = 3;

    const LENGTH = 9;

    public function build(Conversation $conversation): string
    {
        $ranking = $this->getMostReactedMessage($conversation);

        return $this->render('modules/most-reacted-message.html.twig', ['ranking' => $ranking]);
    }

    private function getMostReactedMessage(Conversation $conversation): array
    {
        $query = $this->createQueryBuilder()
            ->select('p.name, m.content, m.nbReactions, m.datetime')
            ->from(Person::class, 'p')
            ->where('p.conversation = :conversation_id')
            ->join(Message::class, 'm', 'WITH', 'p.id = m.author')
            ->orderBy('m.nbReactions', 'DESC')
            ->setMaxResults(self::LENGTH)
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();

        return $query->execute();
    }
}
