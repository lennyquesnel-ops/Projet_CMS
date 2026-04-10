<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class BlocCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bloc::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            ChoiceField::new('type')
                ->setChoices([
                    'Bloc WYSIWYG' => 'wysiwyg',
                ]),

            AssociationField::new('page')
                ->setFormTypeOption('choice_label', 'slug'),

            IntegerField::new('ordre'),

            BooleanField::new('est_visible'),

            TextareaField::new('contenu')
                ->setFormType(CKEditorType::class)
                ->setFormTypeOptions([
                    'config_name' => 'bloc_config',
                ])
                ->hideOnIndex(),
        ];
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