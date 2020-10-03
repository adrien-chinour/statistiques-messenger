<?php

namespace App\Module;

use App\Entity\Conversation;

interface ModuleInterface
{
    /**
     * Main method to build module
     *
     * @param Conversation $conversation
     * @return string
     */
    public function build(Conversation $conversation): string;

}
