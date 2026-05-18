<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\User;
use App\Repository\PageRepository;
use App\Security\Voter\PageVoter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

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
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier la page')
            ->setEntityPermission('ROLE_USER');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('slug', 'Slug')
            ->setRequired(true)
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
        $qb = $this->pageRepository->addAdminIndexJoins($qb);

        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN') || !$user instanceof User) {
            return $qb;
        }

        return $qb
            ->leftJoin('entity.profilsAcces', 'profilAccesFiltre')
            ->leftJoin('profilAccesFiltre.users', 'userFiltre')
            ->andWhere('userFiltre = :utilisateurConnecte')
            ->setParameter('utilisateurConnecte', $user)
            ->distinct();
    }

    public function edit(AdminContext $context): KeyValueStore|Response
    {
        $page = $context->getEntity()->getInstance();

        if (!$page instanceof Page) {
            throw $this->createAccessDeniedException();
        }

        $this->denyAccessUnlessGranted(PageVoter::EDIT, $page);

        return parent::edit($context);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::INDEX, 'ROLE_USER')
            ->setPermission(Action::EDIT, 'ROLE_USER')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->disable(Action::DETAIL)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter une page')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $action) => $action->displayIf(
                    fn (Page $page): bool => $this->isGranted(PageVoter::EDIT, $page)
                )
            );
    }
}