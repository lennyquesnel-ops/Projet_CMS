<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends StayOnEditCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle(Crud::PAGE_INDEX, 'Utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un utilisateur')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier l’utilisateur')
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('uuid', 'Identifiant')
            ->setRequired(true)
            ->setColumns(6);

        $plainPasswordField = TextField::new('plainPassword', 'Mot de passe')
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms()
            ->setColumns(6);

        if ($pageName === Crud::PAGE_EDIT) {
            $plainPasswordField->setHelp('Laisse vide pour ne pas modifier le mot de passe.');
        }

        yield $plainPasswordField;

        yield ChoiceField::new('roles', 'Rôles Symfony')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur limité' => 'ROLE_USER',
            ])
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setRequired(true)
            ->setHelp('ROLE_ADMIN = accès total. ROLE_USER = accès limité selon les profils d’accès.')
            ->setColumns(12);

        yield AssociationField::new('profilsAcces', 'Profils d’accès aux pages')
            ->autocomplete()
            ->setRequired(false)
            ->setHelp('Choisis les profils qui donnent accès à certaines pages.')
            ->setColumns(12);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPasswordIfNeeded($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->hashPasswordIfNeeded($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPasswordIfNeeded(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($hashedPassword);
        $user->setPlainPassword(null);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }
}