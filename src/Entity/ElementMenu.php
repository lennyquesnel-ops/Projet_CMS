<?php

namespace App\Entity;

use App\Repository\ElementMenuRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ElementMenuRepository::class)]
class ElementMenu
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

    #[ORM\OneToOne(mappedBy: 'elementMenu', cascade: ['persist', 'remove'])]
    private ?Bloc $bloc = null;

    #[ORM\ManyToOne(inversedBy: 'elementMenu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(inversedBy: 'elementMenu')]
    private ?Page $page = null;

    #[Assert\Callback]
    public function validateBlocEtPage(ExecutionContextInterface $context): void
    {
        if ($this->bloc === null || $this->page === null) {
            return;
        }

        if ($this->bloc->getPage() !== $this->page) {
            $context->buildViolation('Le bloc sélectionné doit appartenir à la page choisie.')
                ->atPath('bloc')
                ->addViolation();
        }
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

    public function getBloc(): ?Bloc
    {
        return $this->bloc;
    }

    public function setBloc(?Bloc $bloc): static
    {
        if ($this->bloc === $bloc) {
            return $this;
        }

        if ($this->bloc !== null) {
            $oldBloc = $this->bloc;
            $this->bloc = null;

            if ($oldBloc->getElementMenu() === $this) {
                $oldBloc->setElementMenu(null);
            }
        }

        $this->bloc = $bloc;

        if ($bloc !== null && $bloc->getElementMenu() !== $this) {
            $bloc->setElementMenu($this);
        }

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
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

    public function __toString(): string
    {
        return $this->libelle ?? 'Nouvel élément de menu';
    }
}