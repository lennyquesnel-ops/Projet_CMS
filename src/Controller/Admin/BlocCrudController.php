<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use App\Repository\BlocRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['libelle', 'contenu', 'pageBlocs.page.slug'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Blocs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un bloc')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le bloc');
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addFieldset('Informations du bloc');

        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('libelle', 'Libellé')
            ->setHelp('Nom interne visible dans l’admin. Exemple : Accueil, Services, Contact.')
            ->setColumns(6);

        yield TextField::new('resumePages', 'Pages où ce bloc est utilisé')
            ->onlyOnIndex();

        yield BooleanField::new('est_visible', 'Visible')
            ->renderAsSwitch(false)
            ->setHelp('Décoche pour préparer un bloc sans l’afficher sur le site.')
            ->setColumns(4);

        yield FormField::addFieldset('Contenu');

        yield TextareaField::new('contenu', 'Contenu')
            ->setFormType(CKEditorType::class)
            ->setFormTypeOptions([
                'config_name' => 'bloc_config',
            ])
            ->setHelp('Tu peux écrire du HTML Bootstrap directement dans CKEditor. Exemple : container, row, col-md-6, btn, card…')
            ->hideOnIndex()
            ->setColumns(12);
    }

    public function createEntity(string $entityFqcn): Bloc
    {
        $bloc = new Bloc();
        $bloc->setEstVisible(true);
        $bloc->setContenu(
            '<section class="container py-5">' . PHP_EOL .
            '    <div class="row">' . PHP_EOL .
            '        <div class="col-12">' . PHP_EOL .
            '            <h2>Nouveau bloc</h2>' . PHP_EOL .
            '            <p>Remplace ce contenu par ta mise en page Bootstrap.</p>' . PHP_EOL .
            '        </div>' . PHP_EOL .
            '    </div>' . PHP_EOL .
            '</section>'
        );

        return $bloc;
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