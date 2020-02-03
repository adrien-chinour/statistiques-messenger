<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reaction")
 */
class Reaction
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var integer
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="reactions")
     * @var Person
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="reactions")
     * @var Message
     */
    private $message;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $content;

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
     * @return Reaction
     */
    public function setAuthor(Person $author): Reaction
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return Reaction
     */
    public function setMessage(Message $message): Reaction
    {
        $this->message = $message;
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
     * @return Reaction
     */
    public function setContent(string $content): Reaction
    {
        $this->content = $content;
        return $this;
    }

}
