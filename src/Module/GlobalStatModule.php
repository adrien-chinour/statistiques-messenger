<?php

namespace App\Module;

use App\Entity\Conversation;
use App\Entity\Message;

/**
 * This module provide global statistics : number of message, average day message
 */
final class GlobalStatModule extends Module
{

    public int $weight = 100;

    public function build(Conversation $conversation): string
    {
        // number of message
        $count = $this->countMessages($conversation);

        // average message per day
        $diff = date_diff($this->getStartDate($conversation), $this->getEndDate($conversation));
        $ratio = $this->countMessages($conversation) / $diff->days;

        return $this->render("modules/global-stat.html.twig", [
            "count" => $count,
            "ratio" => $ratio
        ]);
    }

    private function countMessages(Conversation $conversation): int
    {
        $query = $this->createQueryBuilder()
            ->select('count(m.id)')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->getQuery();

        return $query->execute()[0][1];
    }

    private function getStartDate(Conversation $conversation)
    {
        $query = $this->createQueryBuilder()
            ->select('m.datetime')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->orderBy('m.datetime')
            ->setMaxResults(1)
            ->getQuery();

        return $query->execute()[0]['datetime'];
    }

    private function getEndDate(Conversation $conversation)
    {
        $query = $this->createQueryBuilder()
            ->select('m.datetime')
            ->from(Message::class, 'm')
            ->where('m.conversation = :conversation_id')
            ->setParameter('conversation_id', $conversation->getId())
            ->orderBy('m.datetime', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->execute()[0]['datetime'];
    }
}
