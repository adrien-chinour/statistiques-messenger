<?php

namespace App\Core\Module;

use App\Core\Entity\Conversation;

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
