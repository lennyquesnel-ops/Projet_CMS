<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug existe déjà.')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\OneToMany(targetEntity: ElementMenu::class, mappedBy: 'page')]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    private Collection $elementMenu;

    #[ORM\OneToMany(targetEntity: Bloc::class, mappedBy: 'page')]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    private Collection $bloc;

    public function __construct()
    {
        $this->elementMenu = new ArrayCollection();
        $this->bloc = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getElementMenu(): Collection
    {
        return $this->elementMenu;
    }

    public function addElementMenu(ElementMenu $elementMenu): static
    {
        if (!$this->elementMenu->contains($elementMenu)) {
            $this->elementMenu->add($elementMenu);
            $elementMenu->setPage($this);
        }

        return $this;
    }

    public function removeElementMenu(ElementMenu $elementMenu): static
    {
        if ($this->elementMenu->removeElement($elementMenu)) {
            if ($elementMenu->getPage() === $this) {
                $elementMenu->setPage(null);
            }
        }

        return $this;
    }

    public function getBloc(): Collection
    {
        return $this->bloc;
    }

    public function addBloc(Bloc $bloc): static
    {
        if (!$this->bloc->contains($bloc)) {
            $this->bloc->add($bloc);
            $bloc->setPage($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): static
    {
        if ($this->bloc->removeElement($bloc)) {
            if ($bloc->getPage() === $this) {
                $bloc->setPage(null);
            }
        }

        return $this;
    }

    public function getResumeBlocs(): string
    {
        $labels = [];

        foreach ($this->getBloc() as $bloc) {
            $labels[] = $bloc->getLibelle()
                ?: sprintf('Bloc #%d - %s', $bloc->getId(), $bloc->getType() ?? 'sans type');
        }

        return $labels !== [] ? implode(', ', $labels) : '—';
    }

    public function getResumeElementsMenu(): string
    {
        $labels = [];

        foreach ($this->getElementMenu() as $elementMenu) {
            $menuLabel = $elementMenu->getMenu()?->getLibelle();
            $elementLabel = $elementMenu->getLibelle();

            $labels[] = $menuLabel
                ? sprintf('%s > %s', $menuLabel, $elementLabel)
                : ($elementLabel ?? '—');
        }

        return $labels !== [] ? implode(', ', $labels) : '—';
    }

    public function __toString(): string
    {
        return $this->slug ?? 'Nouvelle page';
    }
}