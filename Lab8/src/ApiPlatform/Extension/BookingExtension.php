<?php

declare(strict_types=1);

namespace App\ApiPlatform\Extension;

use App\Entity\Booking;
use Doctrine\ORM\QueryBuilder;

class BookingExtension extends AbstractCurrentUserExtension
{
    public function getResourceClass(): string
    {
        return Booking::class;
    }

    public function buildQuery(QueryBuilder $queryBuilder): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_ARRAY];

        // join на client, якщо ще немає
        $joins = $queryBuilder->getDQLPart('join');
        $hasClientJoin = false;

        if (isset($joins[$rootAlias])) {
            foreach ($joins[$rootAlias] as $join) {
                if ($join->getAlias() === 'client') {
                    $hasClientJoin = true;
                    break;
                }
            }
        }

        if (!$hasClientJoin) {
            $queryBuilder->join($rootAlias . '.client', 'client');
        }

        $queryBuilder
            ->andWhere('client.email = :current_email')
            ->setParameter('current_email', $user->getUserIdentifier());
    }
}
