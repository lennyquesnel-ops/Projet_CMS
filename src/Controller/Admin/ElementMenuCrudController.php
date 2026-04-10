<?php

namespace App\Controller\Admin;

use App\Entity\ElementMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ElementMenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ElementMenu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Élément de menu')
            ->setEntityLabelInPlural('Éléments de menu')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('libelle', 'Libellé'),

            IntegerField::new('ordre', 'Ordre'),

            BooleanField::new('est_visible', 'Visible'),

            AssociationField::new('menu', 'Menu parent')
                ->setFormTypeOption('choice_label', 'libelle'),

            AssociationField::new('page', 'Page liée')
                ->setFormTypeOption('choice_label', 'slug'),

            // On cache le bloc ici pour ne pas polluer le CRUD menu
            AssociationField::new('bloc', 'Bloc')
                ->hideOnForm()
                ->hideOnIndex()
                ->hideOnDetail(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un élément de menu')
            );
    }
}