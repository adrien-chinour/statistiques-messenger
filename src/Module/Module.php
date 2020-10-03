<?php

namespace App\Module;

use App\Service\Renderer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

abstract class Module implements ModuleInterface
{
    public int $weight = 0;

    private Renderer $renderer;

    private EntityManager $em;

    public function __construct(EntityManager $entityManager, Renderer $renderer)
    {
        $this->renderer = $renderer;
        $this->em = $entityManager;
    }

    public function render(string $template, array $data = []): string
    {
        try {
            return $this->renderer->write($template, $data);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf("Build module %s failed. Exception message : %s", get_class(), $e->getMessage())
            );
        }
    }

    protected function createQueryBuilder()
    {
        return new QueryBuilder($this->em);
    }


}
