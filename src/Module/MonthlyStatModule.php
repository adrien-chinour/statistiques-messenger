<?php

namespace App\Module;

use App\Core\Entity\Conversation;
use App\Core\Module\AbstractModule;

class MonthlyStatModule extends AbstractModule
{
    public int $weight = 3;

    public function build(Conversation $conversation): string
    {
        return $this->render('modules/monthly-stat.html.twig', [
            'months' => $this->getMessageByMonth()
        ]);
    }

    protected function getMessageByMonth(): array
    {
        // to_char is not known by doctrine dql
        $sql = "select to_char(m.datetime, 'YYYY-MM') as date, count(*) as count from message m group by to_char(m.datetime, 'YYYY-MM')";

        $connection = $this->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();

        $data = $statement->fetchAll();
        usort($data, fn($a, $b) => ($a['date'] < $b['date']) ? -1 : 1);

        return $data;
    }
}
