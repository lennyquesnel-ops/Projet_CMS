<?php

namespace App\Twig;

use App\Repository\MenuRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly MenuRepository $menuRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_menus', [$this, 'getMenus']),
        ];
    }

    public function getMenus(): array
    {
        return $this->menuRepository->findVisibleWithElementsPagesAndBlocs();
    }
}