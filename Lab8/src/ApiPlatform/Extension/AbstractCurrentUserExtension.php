<?php

declare(strict_types=1);

namespace App\ApiPlatform\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractCurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public const FIRST_ELEMENT_ARRAY = 0;
    public const ADMIN_ROLES = ['ROLE_ADMIN'];

    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        if ($this->isFiltering($operationName, $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }

    protected function isFiltering(?string $operationName, string $resourceClass): bool
    {
        $user = $this->security->getUser();

        return !$this->apply($operationName)
            || $resourceClass !== $this->getResourceClass()
            || ($user && count(array_intersect(self::ADMIN_ROLES, $user->getRoles())) > 0);
    }

    protected function apply(?string $operationName): bool
    {
        // застосовуємо тільки для GET-операцій колекції
        if ($operationName === null) {
            return true;
        }

        return str_contains($operationName, 'get');
    }

    abstract public function getResourceClass(): string;

    abstract public function buildQuery(QueryBuilder $queryBuilder): void;

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        if ($this->isFiltering($operationName, $resourceClass)) {
            return;
        }

        $this->buildQuery($queryBuilder);
    }
}
