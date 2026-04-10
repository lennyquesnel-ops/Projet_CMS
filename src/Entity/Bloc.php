<?php

namespace App\Entity;

use App\Repository\BlocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
class Bloc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?int $ordre = null;

    #[ORM\Column]
    private ?bool $est_visible = null;

    #[ORM\OneToOne(inversedBy: 'bloc', cascade: ['persist', 'remove'])]
    private ?ElementMenu $elementMenu = null;

    /**
     * @var Collection<int, media>
     */
    #[ORM\ManyToMany(targetEntity: Media::class, inversedBy: 'blocs')]
    private Collection $medias;

    #[ORM\ManyToOne(inversedBy: 'bloc')]
    private ?Page $page = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
     * @return Collection<int, media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    public function removeMedia(media $media): static
    {
        $this->medias->removeElement($media);

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
        return sprintf(
            'Bloc #%s - %s',
            $this->id ?? '?',
            $this->type ?? 'sans type'
        );
    }
}
