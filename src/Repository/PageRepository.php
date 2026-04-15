<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function addAdminIndexJoins(QueryBuilder $qb): QueryBuilder
    {
        return $qb
            ->leftJoin('entity.bloc', 'b')->addSelect('b')
            ->leftJoin('entity.elementMenu', 'em')->addSelect('em')
            ->leftJoin('em.menu', 'm')->addSelect('m')
            ->leftJoin('em.bloc', 'emb')->addSelect('emb')
            ->distinct();
    }

    public function findOneBySlugWithBlocs(string $slug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.bloc', 'b')
            ->addSelect('b')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('b.ordre', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findHomepageSlug(): ?string
    {
        $result = $this->createQueryBuilder('p')
            ->select('p.slug')
            ->where('p.slug = :accueil')
            ->setParameter('accueil', 'accueil')
            ->getQuery()
            ->getOneOrNullResult();

        if ($result !== null) {
            return $result['slug'];
        }

        $result = $this->createQueryBuilder('p')
            ->select('p.slug')
            ->where('p.slug = :home')
            ->setParameter('home', 'home')
            ->getQuery()
            ->getOneOrNullResult();

        if ($result !== null) {
            return $result['slug'];
        }

        $result = $this->createQueryBuilder('p')
            ->select('p.slug')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result['slug'] ?? null;
    }
}