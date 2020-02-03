<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="messages")
     * @var Person
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages")
     * @var Conversation
     */
    private $conversation;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $datetime;

    /**
     * @ORM\OneToMany(targetEntity="Reaction", mappedBy="message")
     * @var Reaction[]
     */
    private $reactions;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getAuthor(): Person
    {
        return $this->author;
    }

    /**
     * @param Person $author
     * @return Message
     */
    public function setAuthor(Person $author): Message
    {
        $this->author = $author;
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
     * @return Message
     */
    public function setConversation(Conversation $conversation): Message
    {
        $this->conversation = $conversation;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Message
     */
    public function setContent(string $content): Message
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatetime(): DateTime
    {
        return $this->datetime;
    }

    /**
     * @param DateTime $datetime
     * @return Message
     */
    public function setDatetime(DateTime $datetime): Message
    {
        $this->datetime = $datetime;
        return $this;
    }

    /**
     * @return Reaction[]
     */
    public function getReactions(): array
    {
        return $this->reactions;
    }

    /**
     * @param Reaction[] $reactions
     * @return Message
     */
    public function setReactions(array $reactions): Message
    {
        $this->reactions = $reactions;
        return $this;
    }

}
