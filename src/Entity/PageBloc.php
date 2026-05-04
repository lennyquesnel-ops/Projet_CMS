<?php

namespace App\Entity;

use App\Repository\PageBlocRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageBlocRepository::class)]
class PageBloc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pageBlocs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Page $page = null;

    #[ORM\ManyToOne(inversedBy: 'pageBlocs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Bloc $bloc = null;

    #[ORM\Column(options: ['default' => 1])]
    private ?int $ordre = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getBloc(): ?Bloc
    {
        return $this->bloc;
    }

    public function setBloc(?Bloc $bloc): static
    {
        $this->bloc = $bloc;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function __toString(): string
    {
        $page = $this->page?->getSlug() ?? 'Page inconnue';
        $bloc = $this->bloc?->getLibelle() ?? 'Bloc inconnu';

        return $page . ' > ' . $bloc;
    }
}