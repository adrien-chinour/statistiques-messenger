<?php

namespace App\Core\Module;

use App\Core\Renderer;
use App\Core\RenderModuleException;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractModule implements ModuleInterface
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
            throw new RenderModuleException($this, $e);
        }
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->em);
    }

    protected function getConnection(): Connection
    {
        return $this->em->getConnection();
    }
}
