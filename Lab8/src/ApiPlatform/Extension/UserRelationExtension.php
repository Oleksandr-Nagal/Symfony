<?php

declare(strict_types=1);

namespace App\ApiPlatform\Extension;

use Doctrine\ORM\QueryBuilder;

abstract class UserRelationExtension extends AbstractCurrentUserExtension
{
    public function buildQuery(QueryBuilder $queryBuilder): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            // Якщо не залогінений – нічого не фільтруємо (або можна зробити порожню вибірку)
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_ARRAY];

        // Тут очікуємо, що в сутності є поле email / або зв'язок на user
        $queryBuilder
            ->andWhere($rootAlias . '.email = :current_email')
            ->setParameter('current_email', $user->getUserIdentifier());
    }
}
