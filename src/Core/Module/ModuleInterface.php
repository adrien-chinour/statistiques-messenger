<?php

namespace App\Core\Module;

use App\Core\Entity\Conversation;

interface ModuleInterface
{
    /**
     * Main method to build module
     *
     * @param Conversation $conversation : conversation to load for module
     * @return string                    : HTML output from module
     */
    public function build(Conversation $conversation): string;

}
