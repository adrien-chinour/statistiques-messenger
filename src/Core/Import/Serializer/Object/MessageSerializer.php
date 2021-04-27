<?php

namespace App\Core\Import\Serializer\Object;

use App\Core\Entity\Media;
use App\Core\Entity\Message;
use App\Core\Import\ConversationContext;
use App\Core\Import\Serializer\ObjectDenormalize;

class MessageSerializer implements ObjectDenormalize
{
    private MediaSerializer $mediaSerializer;

    private ReactionSerializer $reactionSerializer;

    public function __construct(MediaSerializer $mediaSerializer, ReactionSerializer $reactionSerializer)
    {
        $this->mediaSerializer = $mediaSerializer;
        $this->reactionSerializer = $reactionSerializer;
    }

    public function denormalize(array $data, ConversationContext $context): Message
    {
        $message = (new Message())
            ->setConversation($context->conversation())
            ->setNbReactions(count($data['reactions'] ?? []))
            ->setDatetime(new \DateTime(sprintf("@%d", round($data['timestamp_ms'] / 1000))));

        if (null === ($author = $context->getPerson($data['sender_name']))) {
            throw new \LogicException("user cannot be null");
        }
        $message->setAuthor($author);

        /** @var Media[] $medias */
        $medias = [];
        foreach (["audio_files" => Media::AUDIO, "files" => Media::FILE, "photos" => Media::PHOTO, "gifs" => Media::GIF, "videos" => Media::VIDEO] as $key => $type) {
            if (isset($data[$key])) {
                $medias = array_merge($medias, array_map(fn($mediaArray) => $this->mediaSerializer->denormalize($mediaArray, $context, $type), $data[$key]));
            }
        }

        if (!empty($medias)) {
            foreach ($medias as $media) {
                $media->setMessage($message);
            }
            $message->setMedias($medias);
        }

        if (isset($data["reactions"])) {
            $reactions = [];
            foreach ($data["reactions"] as $reactionData) {
                $reaction = $this->reactionSerializer->denormalize($reactionData, $context);
                $reaction->setMessage($message);
                $reactions[] = $reaction;
            }
            $message->setReactions($reactions);
        }

        if (isset($data['content'])) {
            $message->setContent($data['content']);
        }

        return $message;
    }
}
