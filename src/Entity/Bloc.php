<?php

namespace App\Entity;

use App\Repository\BlocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
class Bloc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Le libellé est obligatoire.')]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?bool $est_visible = null;

    #[ORM\OneToOne(inversedBy: 'bloc', cascade: ['persist', 'remove'])]
    private ?ElementMenu $elementMenu = null;

    #[ORM\ManyToMany(targetEntity: Media::class, inversedBy: 'blocs')]
    private Collection $medias;

    /**
     * @var Collection<int, PageBloc>
     */
    #[ORM\OneToMany(mappedBy: 'bloc', targetEntity: PageBloc::class, orphanRemoval: true)]
    private Collection $pageBlocs;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->pageBlocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

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

    public function getElementMenu(): ?ElementMenu
    {
        return $this->elementMenu;
    }

    public function setElementMenu(?ElementMenu $elementMenu): static
    {
        $this->elementMenu = $elementMenu;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        $this->medias->removeElement($media);

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
            $pageBloc->setBloc($this);
        }

        return $this;
    }

    public function removePageBloc(PageBloc $pageBloc): static
    {
        if ($this->pageBlocs->removeElement($pageBloc)) {
            if ($pageBloc->getBloc() === $this) {
                $pageBloc->setBloc(null);
            }
        }

        return $this;
    }

    public function isUsedInPage(): bool
    {
        return !$this->pageBlocs->isEmpty();
    }

    public function getResumePages(): string
    {
        $labels = [];

        foreach ($this->getPageBlocs() as $pageBloc) {
            $page = $pageBloc->getPage();

            if ($page === null) {
                continue;
            }

            $labels[] = sprintf('%s (ordre %d)', $page->getSlug(), $pageBloc->getOrdre());
        }

        return $labels !== [] ? implode(', ', $labels) : '—';
    }

    public function __toString(): string
    {
        return $this->libelle
            ?: sprintf('Bloc #%s', $this->id ?? '?');
    }
}