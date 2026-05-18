<?php

namespace App\Controller\Admin;

use App\Entity\ProfilAcces;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfilAccesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProfilAcces::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Profil d’accès')
            ->setEntityLabelInPlural('Profils d’accès')
            ->setPageTitle(Crud::PAGE_INDEX, 'Profils d’accès')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un profil d’accès')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le profil d’accès')
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('nom', 'Nom')
            ->setRequired(true)
            ->setHelp('Exemple : Rédacteur accueil, Rédacteur services...')
            ->setColumns(6);

        yield TextareaField::new('description', 'Description')
            ->setRequired(false)
            ->hideOnIndex()
            ->setColumns(12);

        yield AssociationField::new('pages', 'Pages modifiables')
            ->autocomplete()
            ->setRequired(false)
            ->setHelp('Choisis les pages que les utilisateurs de ce profil pourront modifier.')
            ->setColumns(12);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un profil')
            );
    }
}