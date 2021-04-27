<?php

namespace App\Core\Exception;

use App\Core\Module\ModuleInterface;

class RenderModuleException extends \Exception
{
    public function __construct(ModuleInterface $module, \Throwable $previous = null)
    {
        parent::__construct(sprintf("Build module '%s' failed.", get_class($module)), 0, $previous);
    }
}
