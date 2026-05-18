<?php

namespace App\Twig;

use App\Entity\Parametre;
use App\Repository\ParametreRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteExtension extends AbstractExtension
{
    public function __construct(
        private readonly ParametreRepository $parametreRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_parametre', [$this, 'getParametre']),
            new TwigFunction('get_parametre_value', [$this, 'getParametreValue']),
        ];
    }

    public function getParametre(string $code): ?Parametre
    {
        return $this->parametreRepository->findOneByCode($code);
    }

    public function getParametreValue(string $code, ?string $default = null): ?string
    {
        return $this->parametreRepository->findValueByCode($code, $default);
    }
}