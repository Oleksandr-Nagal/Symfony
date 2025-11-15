<?php

namespace App\Repository;

use App\Entity\Actor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Actor>
 */
class ActorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actor::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllActorsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('actor');

        if (!empty($data['name'])) {
            $queryBuilder->andWhere('actor.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);

        $queryBuilder->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'actors' => $queryBuilder->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
