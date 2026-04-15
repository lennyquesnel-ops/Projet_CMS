<?php

namespace App\Controller\Admin;

use App\Entity\Parametre;
use App\Repository\MediaRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ParametreCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly MediaRepository $mediaRepository,
        private readonly string $blocsUploadPath,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Parametre::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Paramètre')
            ->setEntityLabelInPlural('Paramètres');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield TextField::new('url', 'URL du site');

        yield ChoiceField::new('logo_site', 'Logo du site')
            ->setChoices($this->getLogoChoices())
            ->autocomplete()
            ->renderAsNativeWidget(false)
            ->setHelp('Choisis un média de la médiathèque. Son chemin sera enregistré automatiquement dans logo_site.');

        yield ImageField::new('logo_site', 'Aperçu du logo')
            ->setBasePath($this->blocsUploadPath)
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un paramètre')
            );
    }

    private function getLogoChoices(): array
    {
        $choices = [];

        foreach ($this->mediaRepository->findLogoChoices() as $media) {
            $libelle = $media['libelle_media'] ?: 'Média sans libellé';
            $chemin = $media['chemin'] ?? null;

            if ($chemin === null || $chemin === '') {
                continue;
            }

            $choices[sprintf('%s (%s)', $libelle, $chemin)] = $chemin;
        }

        return $choices;
    }
}