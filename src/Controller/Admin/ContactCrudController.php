<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message de contact')
            ->setEntityLabelInPlural('Messages de contact')
            ->setPageTitle(Crud::PAGE_INDEX, 'Messages de contact')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détail du message')
            ->setDefaultSort(['date' => 'DESC', 'id' => 'DESC'])
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('nom', 'Nom')
            ->setRequired(true)
            ->setColumns(6);

        yield TextField::new('prenom', 'Prénom')
            ->setRequired(true)
            ->setColumns(6);

        yield EmailField::new('email', 'Email')
            ->setRequired(true)
            ->setColumns(6);

        yield TextField::new('objet', 'Objet')
            ->setRequired(true)
            ->setColumns(6);

        yield TextareaField::new('message', 'Message')
            ->setRequired(true)
            ->hideOnIndex()
            ->setColumns(12);

        yield DateField::new('date', 'Date')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $action) => $action
                    ->setLabel('Supprimer')
                    ->displayIf(fn (?Contact $contact): bool => $contact !== null && $contact->getId() !== null)
            );
    }
}