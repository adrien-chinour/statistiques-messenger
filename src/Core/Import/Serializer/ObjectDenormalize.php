<?php

namespace App\Core\Import\Serializer;

use App\Core\Import\ConversationContext;

interface ObjectDenormalize
{
    public function denormalize(array $data, ConversationContext $context): object;
}
