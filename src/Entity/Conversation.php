<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="conversation")
 */
class Conversation
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
    private $uuid;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="conversation")
     * @var Person[]
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation")
     * @var Message[]
     */
    private $messages;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $folder;

    public function __toString()
    {
        return $this->getName();
    }

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
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return Conversation
     */
    public function setUuid(string $uuid): Conversation
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return Person[]
     */
    public function getPersons(): array
    {
        return $this->persons;
    }

    /**
     * @param Person[] $persons
     * @return Conversation
     */
    public function setPersons(array $persons): Conversation
    {
        $this->persons = $persons;
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
     * @return Conversation
     */
    public function setMessages(array $messages): Conversation
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * @param string $folder
     * @return Conversation
     */
    public function setFolder(string $folder): Conversation
    {
        $this->folder = $folder;
        return $this;
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
     * @return Conversation
     */
    public function setName(string $name): Conversation
    {
        $this->name = $name;
        return $this;
    }

}
