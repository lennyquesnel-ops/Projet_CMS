<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use App\Repository\BlocRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlocCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly BlocRepository $blocRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Bloc::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bloc')
            ->setEntityLabelInPlural('Blocs')
            ->setDefaultSort(['ordre' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('libelle', 'Libellé'),

            ChoiceField::new('type')
                ->setChoices([
                    'Bloc WYSIWYG' => 'wysiwyg',
                ]),

            AssociationField::new('page', 'Page')
                ->setFormTypeOption('choice_label', 'slug'),

            IntegerField::new('ordre', 'Ordre'),

            BooleanField::new('est_visible', 'Visible'),

            TextareaField::new('contenu')
                ->setFormType(CKEditorType::class)
                ->setFormTypeOptions([
                    'config_name' => 'bloc_config',
                ])
                ->hideOnIndex(),
        ];
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        return $this->blocRepository->addAdminIndexJoins($qb);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un bloc')
            );
    }
}