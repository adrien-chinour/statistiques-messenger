<?php

namespace App\Module;

use App\Core\Entity\Conversation;
use App\Core\Entity\Message;
use App\Core\Entity\Person;
use App\Core\Module\AbstractModule;

class UserRankingModule extends AbstractModule
{
    public int $weight = 2;

    public function build(Conversation $conversation): string
    {
        $ranking = array_map(function ($row) {
            $row["ratio"] = $row["nb_reaction"] / $row["nb_message"];
            return $row;
        }, $this->getData($conversation));

        return $this->render("modules/user-ranking.html.twig", ['ranking' => $ranking]);
    }

    private function getData(Conversation $conversation): array
    {
        $query = $this->createQueryBuilder()
            ->select('p.name, sum(m.nbReactions) as nb_reaction, count(m.id) as nb_message')
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
