<?php

namespace App\Repository;

use App\Entity\Parametre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parametre>
 */
class ParametreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parametre::class);
    }

    public function findOneByCode(string $code): ?Parametre
    {
        return $this->findOneBy([
            'code_parametre' => $code,
        ]);
    }

    public function findValueByCode(string $code, ?string $default = null): ?string
    {
        $parametre = $this->findOneByCode($code);

        if (!$parametre) {
            return $default;
        }

        return $parametre->getValeurParametre() ?: $default;
    }
}