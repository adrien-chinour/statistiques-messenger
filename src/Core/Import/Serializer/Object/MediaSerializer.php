<?php

namespace App\Core\Import\Serializer\Object;

use App\Core\Entity\Media;
use App\Core\Import\ConversationContext;
use App\Core\Import\Serializer\ObjectDenormalize;

class MediaSerializer implements ObjectDenormalize
{
    public function denormalize(array $data, ConversationContext $context, ?string $type = null): Media
    {
        return (new Media())->setUri($data['uri'])->setType($type);
    }
}
