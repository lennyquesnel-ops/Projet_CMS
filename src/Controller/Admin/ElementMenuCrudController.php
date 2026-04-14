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

            AssociationField::new('bloc', 'Bloc d’ancrage'),

        ];
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