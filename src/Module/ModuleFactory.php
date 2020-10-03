<?php


namespace App\Module;


use App\Service\Renderer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

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
        $classes = array_map(function ($file) {
            return 'App\\Module\\' . str_replace('.php', '', $file);
        }, scandir(__DIR__));

        $classes = array_filter($classes, function ($class) {
            return class_exists($class);
        });

        $classes = array_filter($classes, function ($class) {
            return in_array(Module::class, class_parents($class));
        });

        usort($classes, function ($a, $b) {
            return get_class_vars($a)["weight"] < get_class_vars($b)["weight"];
        });

        return $classes;
    }

}
