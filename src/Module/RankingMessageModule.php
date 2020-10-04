<?php

namespace App\Module;

use App\Core\Module\AbstractModule;
use App\Core\Entity\Conversation;
use App\Core\Entity\Message;
use App\Core\Entity\Person;

final class RankingMessageModule extends AbstractModule
{
    public int $weight = 0;

    public function build(Conversation $conversation): string
    {
        $ranking = $this->getRanking($conversation);

        return $this->render("modules/ranking-message.html.twig", ["ranking" => $ranking]);
    }

    private function getRanking(Conversation $conversation)
    {
        $query = $this->createQueryBuilder()
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
