<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function findLogoChoices(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.libelle_media AS libelle_media', 'm.chemin AS chemin')
            ->andWhere('m.chemin IS NOT NULL')
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
