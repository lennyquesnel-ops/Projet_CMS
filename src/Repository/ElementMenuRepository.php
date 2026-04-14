<?php

namespace App\Repository;

use App\Entity\ElementMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ElementMenu>
 */
class ElementMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementMenu::class);
    }

    public function addAdminIndexJoins(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->leftJoin('entity.menu', 'm')
            ->addSelect('m')
            ->leftJoin('entity.page', 'p')
            ->addSelect('p')
            ->leftJoin('entity.bloc', 'b')
            ->addSelect('b')
            ->leftJoin('b.page', 'bp')
            ->addSelect('bp')
            ->distinct();
    }
}