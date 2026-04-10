<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_media = null;

    #[ORM\Column(length: 255)]
    private ?string $chemin = null;

    /**
     * @var Collection<int, Bloc>
     */
    #[ORM\ManyToMany(targetEntity: Bloc::class, mappedBy: 'medias')]
    private Collection $blocs;

    public function __construct()
    {
        $this->blocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleMedia(): ?string
    {
        return $this->libelle_media;
    }

    public function setLibelleMedia(string $libelle_media): static
    {
        $this->libelle_media = $libelle_media;

        return $this;
    }

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): static
    {
        $this->chemin = $chemin;

        return $this;
    }

    /**
     * @return Collection<int, Bloc>
     */
    public function getBlocs(): Collection
    {
        return $this->blocs;
    }

    public function addBloc(Media|Bloc $bloc): static
    {
        if (!$this->blocs->contains($bloc)) {
            $this->blocs->add($bloc);
            $bloc->addMedia($this);
        }

        return $this;
    }

    public function removeBloc(Bloc $bloc): static
    {
        if ($this->blocs->removeElement($bloc)) {
            $bloc->removeMedia($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle_media ?? 'Nouveau média';
    }
}