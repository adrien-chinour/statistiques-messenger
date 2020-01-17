<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Table(name="person", indexes={@Index(name="person_index_search", columns={"name", "conversation_id"})})
 */
class Person
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="author")
     * @var Message[]
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="persons")
     * @var Conversation
     */
    private $conversation;

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

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param Message[] $messages
     * @return Person
     */
    public function setMessages(array $messages): Person
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    /**
     * @param Conversation $conversation
     * @return Person
     */
    public function setConversation(Conversation $conversation): Person
    {
        $this->conversation = $conversation;
        return $this;
    }

}
