<?php

namespace App\Controller\Admin;

use App\Entity\PageBloc;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class PageBlocCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageBloc::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bloc de page')
            ->setEntityLabelInPlural('Blocs de page')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('page', 'Page')
            ->onlyOnIndex();

        yield AssociationField::new('bloc', 'Bloc')
            ->autocomplete()
            ->setHelp('Choisis le bloc à afficher dans cette page.')
            ->setColumns(8);

        yield IntegerField::new('ordre', 'Ordre')
            ->setHelp('Plus le nombre est petit, plus le bloc apparaît haut dans la page.')
            ->setColumns(4);
    }
}