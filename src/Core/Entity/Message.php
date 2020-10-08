<?php

namespace App\Core\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="messages")
     */
    private ?Person $author = null;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages")
     */
    private ?Conversation $conversation = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $datetime = null;

    /**
     * @ORM\OneToMany(targetEntity="Reaction", mappedBy="message")
     */
    private ?Collection $reactions = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $nbReactions = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): Person
    {
        return $this->author;
    }

    public function setAuthor(Person $author): Message
    {
        $this->author = $author;
        return $this;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function setConversation(Conversation $conversation): Message
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Message
    {
        $this->content = $content;
        return $this;
    }

    public function getDatetime(): DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(DateTime $datetime): Message
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function setReactions(array $reactions): Message
    {
        $this->reactions = new ArrayCollection($reactions);
        return $this;
    }

    public function getNbReactions(): int
    {
        return $this->nbReactions;
    }

    public function setNbReactions(?int $nbReactions): Message
    {
        $this->nbReactions = $nbReactions;

        return $this;
    }

}
