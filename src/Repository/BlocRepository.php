<?php

namespace App\Repository;

use App\Entity\Bloc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bloc>
 */
class BlocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bloc::class);
    }

    public function addAdminIndexJoins(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->leftJoin('entity.pageBlocs', 'pb')
            ->addSelect('pb')
            ->leftJoin('pb.page', 'p')
            ->addSelect('p')
            ->leftJoin('entity.elementMenu', 'em')
            ->addSelect('em')
            ->leftJoin('entity.medias', 'm')
            ->addSelect('m')
            ->distinct();
    }
}