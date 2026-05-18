<?php

namespace App\Entity;

use App\Repository\ParametreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ParametreRepository::class)]
class Parametre
{
    public const CODE_LOGO_SITE = 'logo_site';
    public const CODE_SITE_URL = 'site_url';
    public const CODE_CUSTOM_CSS = 'ckeditor_css';
    public const CODE_THEME_CSS = 'theme_css';
    public const CODE_JS = 'site_js';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code_parametre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $valeur_parametre = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Champ non enregistré en base.
     * Il sert seulement à recevoir le fichier CSS dans le formulaire EasyAdmin.
     */
    private ?File $fichierCss = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeParametre(): ?string
    {
        return $this->code_parametre;
    }

    public function setCodeParametre(?string $code_parametre): static
    {
        $this->code_parametre = $code_parametre;

        return $this;
    }

    public function getValeurParametre(): ?string
    {
        return $this->valeur_parametre;
    }

    public function setValeurParametre(?string $valeur_parametre): static
    {
        $this->valeur_parametre = $valeur_parametre ?? '';

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFichierCss(): ?File
    {
        return $this->fichierCss;
    }

    public function setFichierCss(?File $fichierCss): static
    {
        $this->fichierCss = $fichierCss;

        return $this;
    }

    public function __toString(): string
    {
        return $this->code_parametre ?? 'Nouveau paramètre';
    }
}