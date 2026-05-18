<?php

namespace App\Controller\Admin;

use App\Entity\ElementMenu;
use App\Repository\ElementMenuRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ElementMenuCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly ElementMenuRepository $elementMenuRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return ElementMenu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Élément de menu')
            ->setEntityLabelInPlural('Éléments de menu')
            ->setDefaultSort(['ordre' => 'ASC'])
            ->setSearchFields([
                'libelle',
                'menu.libelle',
                'page.slug',
                'bloc.libelle',
            ])
            ->setPageTitle(Crud::PAGE_INDEX, 'Éléments de menu')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un élément de menu')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier l’élément de menu');
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

        yield AssociationField::new('menu', 'Menu parent')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/element_menu_menu_link.html.twig');

        yield AssociationField::new('menu', 'Menu parent')
            ->onlyOnForms()
            ->autocomplete()
            ->setFormTypeOption('choice_label', 'libelle')
            ->setColumns(6);

        yield AssociationField::new('page', 'Page liée')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/element_menu_page_link.html.twig');

        yield AssociationField::new('page', 'Page liée')
            ->onlyOnForms()
            ->autocomplete()
            ->setFormTypeOption('choice_label', 'slug')
            ->setHelp('Page vers laquelle cet élément de menu doit envoyer.')
            ->setColumns(6);

        yield AssociationField::new('bloc', 'Bloc d’ancrage')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/element_menu_bloc_link.html.twig');

        yield AssociationField::new('bloc', 'Bloc d’ancrage')
            ->onlyOnForms()
            ->autocomplete()
            ->setHelp('Optionnel : permet d’envoyer directement vers une section précise de la page.')
            ->setColumns(6);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $this->elementMenuRepository->addAdminIndexJoins($qb);
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