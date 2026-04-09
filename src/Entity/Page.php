<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, elementMenu>
     */
    #[ORM\OneToMany(targetEntity: ElementMenu::class, mappedBy: 'page')]
    private Collection $elementMenu;

    /**
     * @var Collection<int, bloc>
     */
    #[ORM\OneToMany(targetEntity: bloc::class, mappedBy: 'page')]
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
            $elementMenu->setPage($this);
        }

        return $this;
    }

    public function removeElementMenu(elementMenu $elementMenu): static
    {
        if ($this->elementMenu->removeElement($elementMenu)) {
            // set the owning side to null (unless already changed)
            if ($elementMenu->getPage() === $this) {
                $elementMenu->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, bloc>
     */
    public function getBloc(): Collection
    {
        return $this->bloc;
    }

    public function addBloc(bloc $bloc): static
    {
        if (!$this->bloc->contains($bloc)) {
            $this->bloc->add($bloc);
            $bloc->setPage($this);
        }

        return $this;
    }

    public function removeBloc(bloc $bloc): static
    {
        if ($this->bloc->removeElement($bloc)) {
            // set the owning side to null (unless already changed)
            if ($bloc->getPage() === $this) {
                $bloc->setPage(null);
            }
        }

        return $this;
    }

}
