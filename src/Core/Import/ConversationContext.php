<?php

namespace App\Core\Import;

use App\Core\Entity\Conversation;
use App\Core\Entity\Person;
use Doctrine\Common\Collections\Collection;

class ConversationContext
{
    private Conversation $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function conversation(): Conversation
    {
        return $this->conversation;
    }

    public function persons(): Collection
    {
        return $this->conversation->getPersons();
    }

    public function getPerson(string $name): ?Person
    {
        return $this->persons()->filter(fn(Person $person) => $person->getName() === $name)->first() ?: null;
    }
}
