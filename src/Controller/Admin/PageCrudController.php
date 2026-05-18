<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PageRepository $pageRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Pages')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une page')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier la page');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('slug', 'Slug')
            ->setHelp('Exemple : accueil, services, contact. Le slug sert dans l’URL.')
            ->setColumns(6);

        yield TextField::new('resumeBlocs', 'Blocs affichés')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/page_blocs_links.html.twig');

        yield TextField::new('resumeElementsMenu', 'Éléments de menu')
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/page_elements_menu_links.html.twig');

        yield CollectionField::new('pageBlocs', 'Blocs affichés sur cette page')
            ->useEntryCrudForm(PageBlocCrudController::class)
            ->allowAdd()
            ->allowDelete()
            ->setFormTypeOption('by_reference', false)
            ->setHelp('Ajoute ici les blocs qui doivent apparaître sur cette page, puis choisis leur ordre.')
            ->onlyOnForms()
            ->setColumns(12);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $this->pageRepository->addAdminIndexJoins($qb);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter une page')
            );
    }
}