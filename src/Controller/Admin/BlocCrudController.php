<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use App\Entity\User;
use App\Repository\BlocRepository;
use App\Security\Voter\BlocVoter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class BlocCrudController extends StayOnEditCrudController
{
    public function __construct(
        private readonly BlocRepository $blocRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Bloc::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets
            ->addCssFile('https://cdn.jsdelivr.net/npm/grapesjs@0.22.16/dist/css/grapes.min.css')
            ->addCssFile('css/grapesjs_admin.css')
            ->addJsFile('https://cdn.jsdelivr.net/npm/grapesjs@0.22.16/dist/grapes.min.js')
            ->addJsFile('js/grapesjs_bloc_editor.js');
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
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le bloc')
            ->setEntityPermission('ROLE_USER');
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
            ->onlyOnIndex()
            ->setTemplatePath('admin/field/bloc_pages_links.html.twig');

        yield BooleanField::new('est_visible', 'Visible')
            ->renderAsSwitch(false)
            ->setHelp('Décoche pour préparer un bloc sans l’afficher sur le site.')
            ->setColumns(4);

        yield FormField::addFieldset('Contenu');

        yield TextareaField::new('contenu', 'Contenu')
            ->setFormTypeOption('attr', [
                'class' => 'js-grapesjs-editor',
                'data-grapesjs-assets-url' => $this->generateUrl('admin_grapesjs_assets'),
                'data-grapesjs-upload-url' => $this->generateUrl('admin_grapesjs_upload_image'),
                'data-grapesjs-bootstrap-css-url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                'data-grapesjs-theme-css-url' => $this->generateUrl('admin_ckeditor_dynamic_style'),
                'data-grapesjs-helpers-css-url' => '/css/grapesjs_canvas_helpers.css',
            ])
            ->setHelp('Éditeur visuel GrapesJS. Les anciens contenus HTML restent compatibles.')
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
            '            <p>Remplace ce contenu avec l’éditeur visuel.</p>' . PHP_EOL .
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
        $qb = $this->blocRepository->addAdminIndexJoins($qb);

        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        if (!$user instanceof User) {
            return $qb->andWhere('1 = 0');
        }

        return $qb
            ->leftJoin('p.profilsAcces', 'profilAccesFiltre')
            ->leftJoin('profilAccesFiltre.users', 'userFiltre')
            ->andWhere('userFiltre = :utilisateurConnecte')
            ->setParameter('utilisateurConnecte', $user)
            ->distinct();
    }

    public function edit(AdminContext $context): KeyValueStore|Response
    {
        $bloc = $context->getEntity()->getInstance();

        if (!$bloc instanceof Bloc) {
            throw $this->createAccessDeniedException();
        }

        $this->denyAccessUnlessGranted(BlocVoter::EDIT, $bloc);

        return parent::edit($context);
    }

    public function delete(AdminContext $context): Response
    {
        $bloc = $context->getEntity()->getInstance();

        if (!$bloc instanceof Bloc) {
            throw $this->createAccessDeniedException();
        }

        if ($bloc->isUsedInPage()) {
            $this->addFlash(
                'danger',
                'Ce bloc ne peut pas être supprimé car il est utilisé dans une ou plusieurs pages. Retire-le d’abord des pages concernées.'
            );

            $referrer = $context->getRequest()->headers->get('referer');

            return $this->redirect($referrer ?? $this->generateUrl('admin_bloc_index'));
        }

        $this->denyAccessUnlessGranted(BlocVoter::DELETE, $bloc);

        return parent::delete($context);
    }

    public function configureActions(Actions $actions): Actions
    {
        $blocUsedMessage = Action::new('blocUsedMessage', 'Rattaché à une page', 'fa fa-lock')
            ->linkToUrl('#')
            ->setCssClass('btn btn-secondary disabled text-muted')
            ->setHtmlAttributes([
                'title' => 'Ce bloc est rattaché à une page, il ne peut donc pas être supprimé.',
                'style' => 'pointer-events: none; cursor: default;',
            ])
            ->displayIf(
                fn (Bloc $bloc): bool => $bloc->isUsedInPage()
            );

        return $actions
            ->setPermission(Action::INDEX, 'ROLE_USER')
            ->setPermission(Action::NEW, 'ROLE_USER')
            ->setPermission(Action::EDIT, 'ROLE_USER')
            ->setPermission(Action::DELETE, 'ROLE_USER')
            ->disable(Action::DETAIL)

            ->add(Crud::PAGE_INDEX, $blocUsedMessage)

            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un bloc')
            )

            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $action) => $action
                    ->setLabel('Modifier')
                    ->setIcon('fa fa-pen')
                    ->displayIf(
                        fn (Bloc $bloc): bool => $this->isGranted(BlocVoter::EDIT, $bloc)
                    )
            )

            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $action) => $action
                    ->setLabel('Supprimer')
                    ->setIcon('fa fa-trash')
                    ->setHtmlAttributes(['title' => 'Supprimer'])
                    ->displayIf(
                        fn (Bloc $bloc): bool => !$bloc->isUsedInPage()
                            && $this->isGranted(BlocVoter::DELETE, $bloc)
                    )
            );
    }
}