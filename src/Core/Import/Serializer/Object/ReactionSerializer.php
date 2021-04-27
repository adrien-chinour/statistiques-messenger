<?php

namespace App\Core\Import\Serializer\Object;

use App\Core\Entity\Reaction;
use App\Core\Import\ConversationContext;
use App\Core\Import\Serializer\ObjectDenormalize;

class ReactionSerializer implements ObjectDenormalize
{
    public function denormalize(array $data, ConversationContext $context): Reaction
    {
        if (null === ($author = $context->getPerson($data['actor']))) {
            throw new \LogicException("user cannot be null");
        }

        return (new Reaction())->setContent($data['reaction'])->setAuthor($author);
    }
}
