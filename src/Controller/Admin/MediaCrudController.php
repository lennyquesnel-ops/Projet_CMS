<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Service\UploadManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MediaCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UploadManager $uploadManager,
        private readonly string $blocsUploadDir,
        private readonly string $blocsUploadPath,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Média')
            ->setEntityLabelInPlural('Médiathèque')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('libelle_media', 'Libellé'),

            ImageField::new('chemin', 'Image')
                ->setBasePath($this->blocsUploadPath)
                ->setUploadDir(str_replace(\dirname(__DIR__, 3).'/', '', $this->blocsUploadDir))
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired($pageName === Crud::PAGE_NEW),

            AssociationField::new('blocs', 'Blocs liés')
                ->autocomplete()
                ->hideOnIndex()
                ->setFormTypeOption('by_reference', false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->update(
            Crud::PAGE_INDEX,
            Action::NEW,
            fn (Action $action) => $action->setLabel('Ajouter un média')
        );
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Media) {
            return;
        }

        $this->uploadManager->deleteBlocImage($entityInstance->getChemin());

        parent::deleteEntity($entityManager, $entityInstance);
    }
}