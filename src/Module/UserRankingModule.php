<?php

namespace App\Module;

use App\Core\Entity\Conversation;
use App\Core\Entity\Message;
use App\Core\Entity\Person;
use App\Core\Entity\Reaction;
use App\Core\Module\AbstractModule;
use Doctrine\ORM\Query\Expr\Join;

class UserRankingModule extends AbstractModule
{
    public int $weight = 2;

    public function build(Conversation $conversation): string
    {
        $ranking = array_map(function ($row) {
            $row["ratio"] = $row["nb_reaction"] / $row["nb_message"];
            $row["reactions_send"] = $this->countReactions($row["id"]);
            return $row;
        }, $this->getData($conversation));

        return $this->render("modules/user-ranking.html.twig", ['ranking' => $ranking]);
    }

    private function getData(Conversation $conversation): array
    {
        $query = $this->createQueryBuilder()
            ->select('p.id, p.name, sum(m.nbReactions) as nb_reaction, count(m.id) as nb_message')
            ->from(Person::class, 'p')
            ->where('p.conversation = :conversation_id')
            ->join(Message::class, 'm', Join::WITH, 'p.id = m.author')
            ->groupBy('p.id, p.name')
            ->orderBy('nb_message', 'DESC')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();

        return $query->execute();
    }

    private function countReactions(int $userId)
    {
        $query = $this->createQueryBuilder()
            ->select('count(p.id) as nb_reactions')
            ->from(Person::class, 'p')
            ->join(Reaction::class, 'r', Join::WITH, 'r.author = p.id')
            ->where('p.id = :id')
            ->setParameter('id', $userId)
            ->getQuery();

        return $query->execute()[0]["nb_reactions"];
    }

}
