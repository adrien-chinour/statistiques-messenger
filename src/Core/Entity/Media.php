<?php

namespace App\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 */
class Media
{
    public const AUDIO = 'audio';

    public const FILE = 'file';

    public const GIF = 'gif';

    public const PHOTO = 'photo';

    public const VIDEO = 'video';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $uri;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $type;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="medias")
     */
    private ?Message $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): Media
    {
        $this->uri = $uri;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): Media
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): Media
    {
        $this->message = $message;
        return $this;
    }
}
