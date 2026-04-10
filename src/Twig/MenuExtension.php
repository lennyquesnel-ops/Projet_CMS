<?php

namespace App\Twig;

use App\Repository\MenuRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class MenuExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly MenuRepository $menuRepository
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'menus' => $this->menuRepository->findBy(
                ['est_visible' => true],
                ['ordre' => 'ASC']
            ),
        ];
    }
}