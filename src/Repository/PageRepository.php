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
            ->leftJoin('entity.pageBlocs', 'pb')->addSelect('pb')
            ->leftJoin('pb.bloc', 'b')->addSelect('b')
            ->leftJoin('entity.elementMenu', 'em')->addSelect('em')
            ->leftJoin('em.menu', 'm')->addSelect('m')
            ->leftJoin('em.bloc', 'emb')->addSelect('emb')
            ->distinct();
    }

    public function findOneBySlugWithBlocs(string $slug): ?Page
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.pageBlocs', 'pb')
            ->addSelect('pb')
            ->leftJoin('pb.bloc', 'b')
            ->addSelect('b')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('pb.ordre', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, Page>
     */
    public function findAllWithBlocs(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.pageBlocs', 'pb')
            ->addSelect('pb')
            ->leftJoin('pb.bloc', 'b')
            ->addSelect('b')
            ->orderBy('p.id', 'ASC')
            ->addOrderBy('pb.ordre', 'ASC')
            ->getQuery()
            ->getResult();
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