<?php

namespace App\Core\Module;

use App\Core\Renderer;
use Doctrine\ORM\EntityManager;

class ModuleFactory
{
    private EntityManager $em;

    private Renderer $renderer;

    public function __construct(EntityManager $manager, Renderer $renderer)
    {
        $this->em = $manager;
        $this->renderer = $renderer;
    }

    /**
     * @return ModuleInterface[]
     */
    public function loadModules(): array
    {
        $classes = $this->getModuleList();
        $modules = [];

        foreach ($classes as $class) {
            $modules[] = new $class($this->em, $this->renderer);
        }

        return $modules;
    }

    private function getModuleList()
    {
        // find classes in src/Module folder
        $classes = array_map(function ($file) {
            return 'App\\Module\\' . str_replace('.php', '', $file);
        }, scandir(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Module'])));

        // check if classes is correctly defined
        $classes = array_filter($classes, function ($class) {
            return class_exists($class);
        });

        // check if classes have abstract Module as parent
        $classes = array_filter($classes, function ($class) {
            return in_array(AbstractModule::class, class_parents($class));
        });

        // order by weight in DESC mode
        usort($classes, function ($a, $b) {
            return get_class_vars($a)["weight"] < get_class_vars($b)["weight"];
        });

        return $classes;
    }

}
