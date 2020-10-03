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
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="reactions", cascade={"persist"}))
     */
    private ?Person $author = null;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="reactions", cascade={"persist"}))
     */
    private ?Message $message = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $content = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): Person
    {
        return $this->author;
    }

    public function setAuthor(Person $author): Reaction
    {
        $this->author = $author;
        return $this;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): Reaction
    {
        $this->message = $message;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Reaction
    {
        $this->content = $content;
        return $this;
    }

}
