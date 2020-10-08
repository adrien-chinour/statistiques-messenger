<?php

namespace App\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $uuid = null;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="conversation", cascade={"persist", "remove"})
     */
    private ?Collection $persons = null;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation", cascade={"persist", "remove"})
     */
    private ?Collection $messages = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $folder = null;

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): Conversation
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function setPersons(array $persons): Conversation
    {
        $this->persons = new ArrayCollection($persons);
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setMessages(array $messages): Conversation
    {
        $this->messages = new ArrayCollection($messages);
        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): Conversation
    {
        $this->folder = $folder;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Conversation
    {
        $this->name = $name;
        return $this;
    }
}
