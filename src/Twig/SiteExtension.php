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
            new TwigFunction('get_site_parametre', [$this, 'getSiteParametre']),
        ];
    }

    public function getSiteParametre(): ?Parametre
    {
        return $this->parametreRepository->findCurrent();
    }
}