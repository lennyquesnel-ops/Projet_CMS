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

    /**
     * @var Collection<int, elementMenu>
     */
    #[ORM\OneToMany(targetEntity: ElementMenu::class, mappedBy: 'menu')]
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

    /**
     * @return Collection<int, elementMenu>
     */
    public function getElementMenu(): Collection
    {
        return $this->elementMenu;
    }

    public function addElementMenu(elementMenu $elementMenu): static
    {
        if (!$this->elementMenu->contains($elementMenu)) {
            $this->elementMenu->add($elementMenu);
            $elementMenu->setMenu($this);
        }

        return $this;
    }

    public function removeElementMenu(elementMenu $elementMenu): static
    {
        if ($this->elementMenu->removeElement($elementMenu)) {
            // set the owning side to null (unless already changed)
            if ($elementMenu->getMenu() === $this) {
                $elementMenu->setMenu(null);
            }
        }

        return $this;
    }
}
