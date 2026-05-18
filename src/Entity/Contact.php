<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(
        max: 32,
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(
        max: 32,
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L’adresse email est obligatoire.')]
    #[Assert\Email(message: 'L’adresse email n’est pas valide.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'L’adresse email ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(
        min: 10,
        max: 3000,
        minMessage: 'Le message doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank(message: 'L’objet est obligatoire.')]
    #[Assert\Length(
        max: 128,
        maxMessage: 'L’objet ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $objet = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): static
    {
        $this->objet = $objet;

        return $this;
    }

    public function __toString(): string
    {
        return $this->objet ?? 'Message de contact';
    }
}