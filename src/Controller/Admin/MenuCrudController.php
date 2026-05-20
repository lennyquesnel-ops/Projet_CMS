<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MenuCrudController extends StayOnEditCrudController
{
    public function __construct(
        private readonly MenuRepository $menuRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')
            ->setDefaultSort(['ordre' => 'ASC'])
            ->setSearchFields(['libelle', 'elementMenu.libelle'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Menus')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un menu')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le menu')
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('libelle', 'Libellé')
            ->setColumns(6);

        yield IntegerField::new('ordre', 'Ordre')
            ->setColumns(3);

        yield BooleanField::new('est_visible', 'Visible')
            ->renderAsSwitch(false)
            ->setColumns(3);

        yield TextField::new('resumeElementsMenu', 'Éléments de menu')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/menu_elements_links.html.twig');
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $this->menuRepository->addAdminIndexJoins($qb);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un menu')
            );
    }
}