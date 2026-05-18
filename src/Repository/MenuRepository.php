<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function addAdminIndexJoins(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->leftJoin('entity.elementMenu', 'em')
            ->addSelect('em')
            ->leftJoin('em.page', 'p')
            ->addSelect('p')
            ->leftJoin('em.bloc', 'b')
            ->addSelect('b')
            ->distinct();
    }

    public function findVisibleWithElementsPagesAndBlocs(): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.elementMenu', 'em')
            ->addSelect('em')
            ->leftJoin('em.page', 'p')
            ->addSelect('p')
            ->leftJoin('em.bloc', 'b')
            ->addSelect('b')
            ->andWhere('m.est_visible = :visible')
            ->setParameter('visible', true)
            ->orderBy('m.ordre', 'ASC')
            ->addOrderBy('em.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}