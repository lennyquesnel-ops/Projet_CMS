<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Média')
            ->setEntityLabelInPlural('Médiathèque')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('libelle_media', 'Libellé'),

            ImageField::new('chemin', 'Image')
                ->setBasePath('uploads/blocs')
                ->setUploadDir('public/uploads/blocs')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired($pageName === Crud::PAGE_NEW),

            AssociationField::new('blocs', 'Blocs liés')
                ->autocomplete()
                ->hideOnIndex()
                ->setFormTypeOption('by_reference', false),
                    ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un média')
            );
    }
}