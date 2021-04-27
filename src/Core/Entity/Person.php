<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="author", cascade={"persist", "remove"})
     */
    private ?Collection $messages = null;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="persons")
     */
    private ?Conversation $conversation = null;

    /**
     * @ORM\OneToMany(targetEntity="Reaction", mappedBy="author", cascade={"persist", "remove"})
     */
    private ?Collection $reactions = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Person
     */
    public function setName(string $name): Person
    {
        $this->name = $name;
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setMessages(array $messages): Person
    {
        $this->messages = new ArrayCollection($messages);
        return $this;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): Person
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function countMessages(): int
    {
        return count($this->messages);
    }

    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): Person
    {
        $this->reactions = new ArrayCollection($reactions);
        return $this;
    }
}
