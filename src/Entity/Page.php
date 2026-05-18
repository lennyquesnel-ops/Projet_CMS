<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug existe déjà.')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le slug est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le slug ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $slug = null;

    #[ORM\OneToMany(targetEntity: ElementMenu::class, mappedBy: 'page')]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    private Collection $elementMenu;

    /**
     * @var Collection<int, PageBloc>
     */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageBloc::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['ordre' => 'ASC'])]
    #[Assert\Valid]
    private Collection $pageBlocs;

    /**
     * @var Collection<int, ProfilAcces>
     */
    #[ORM\ManyToMany(targetEntity: ProfilAcces::class, mappedBy: 'pages')]
    private Collection $profilsAcces;

    public function __construct()
    {
        $this->elementMenu = new ArrayCollection();
        $this->pageBlocs = new ArrayCollection();
        $this->profilsAcces = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validateNomsBlocsUniques(ExecutionContextInterface $context): void
    {
        $nomsDejaUtilises = [];

        foreach ($this->pageBlocs as $pageBloc) {
            $bloc = $pageBloc->getBloc();

            if ($bloc === null) {
                continue;
            }

            $nomBloc = $bloc->getLibelle();

            if ($nomBloc === null || trim($nomBloc) === '') {
                continue;
            }

            $nomNormalise = mb_strtolower(trim($nomBloc));

            if (in_array($nomNormalise, $nomsDejaUtilises, true)) {
                $context->buildViolation('Cette page contient déjà un bloc d’ancrage nommé "{{ nom }}". Chaque bloc d’une même page doit avoir un nom différent.')
                    ->setParameter('{{ nom }}', $nomBloc)
                    ->atPath('pageBlocs')
                    ->addViolation();

                return;
            }

            $nomsDejaUtilises[] = $nomNormalise;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

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

    /**
     * @return Collection<int, PageBloc>
     */
    public function getPageBlocs(): Collection
    {
        return $this->pageBlocs;
    }

    public function addPageBloc(PageBloc $pageBloc): static
    {
        if (!$this->pageBlocs->contains($pageBloc)) {
            $this->pageBlocs->add($pageBloc);
            $pageBloc->setPage($this);
        }

        return $this;
    }

    public function removePageBloc(PageBloc $pageBloc): static
    {
        if ($this->pageBlocs->removeElement($pageBloc)) {
            if ($pageBloc->getPage() === $this) {
                $pageBloc->setPage(null);
            }
        }

        return $this;
    }

    public function getResumeBlocs(): string
    {
        $labels = [];

        foreach ($this->getPageBlocs() as $pageBloc) {
            $bloc = $pageBloc->getBloc();

            if ($bloc === null) {
                continue;
            }

            $labels[] = sprintf(
                '%d - %s',
                $pageBloc->getOrdre(),
                $bloc->getLibelle() ?: sprintf('Bloc #%d', $bloc->getId())
            );
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
        return $this->getSlug() ?: 'Page sans slug';
    }

    /**
     * @return Collection<int, ProfilAcces>
     */
    public function getProfilsAcces(): Collection
    {
        return $this->profilsAcces;
    }

    public function addProfilAcces(ProfilAcces $profilAcces): static
    {
        if (!$this->profilsAcces->contains($profilAcces)) {
            $this->profilsAcces->add($profilAcces);
            $profilAcces->addPage($this);
        }

        return $this;
    }

    public function removeProfilAcces(ProfilAcces $profilAcces): static
    {
        if ($this->profilsAcces->removeElement($profilAcces)) {
            $profilAcces->removePage($this);
        }

        return $this;
    }
}