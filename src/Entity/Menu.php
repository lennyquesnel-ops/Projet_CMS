<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $est_visible = true;

    /**
     * @var Collection<int, ElementMenu>
     */
    #[ORM\OneToMany(targetEntity: ElementMenu::class, mappedBy: 'menu')]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    private Collection $elementMenu;

    public function __construct()
    {
        $this->elementMenu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function isEstVisible(): ?bool
    {
        return $this->est_visible;
    }

    public function setEstVisible(bool $est_visible): static
    {
        $this->est_visible = $est_visible;

        return $this;
    }

    /**
     * @return Collection<int, ElementMenu>
     */
    public function getElementMenu(): Collection
    {
        return $this->elementMenu;
    }

    public function addElementMenu(ElementMenu $elementMenu): static
    {
        if (!$this->elementMenu->contains($elementMenu)) {
            $this->elementMenu->add($elementMenu);
            $elementMenu->setMenu($this);
        }

        return $this;
    }

    public function removeElementMenu(ElementMenu $elementMenu): static
    {
        if ($this->elementMenu->removeElement($elementMenu)) {
            if ($elementMenu->getMenu() === $this) {
                $elementMenu->setMenu(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle ?? 'Nouveau menu';
    }
}