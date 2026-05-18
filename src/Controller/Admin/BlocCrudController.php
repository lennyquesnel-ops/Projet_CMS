<?php

namespace App\Controller\Admin;

use App\Entity\Bloc;
use App\Entity\Page;
use App\Entity\PageBloc;
use App\Entity\User;
use App\Repository\BlocRepository;
use App\Repository\PageRepository;
use App\Security\Voter\BlocVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;

class BlocCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly BlocRepository $blocRepository,
        private readonly PageRepository $pageRepository,
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

        if ($pageName === Crud::PAGE_NEW) {
            yield FormField::addFieldset('Rattachement à une page');

            yield Field::new('pageRattachement', 'Page où ajouter le bloc')
                ->setFormType(EntityType::class)
                ->setFormTypeOptions([
                    'class' => Page::class,
                    'choice_label' => 'slug',
                    'placeholder' => 'Choisir une page',
                    'query_builder' => function (PageRepository $pageRepository): QueryBuilder {
                        $qb = $pageRepository
                            ->createQueryBuilder('page')
                            ->orderBy('page.slug', 'ASC');

                        $user = $this->getUser();

                        if ($this->isGranted('ROLE_ADMIN')) {
                            return $qb;
                        }

                        if (!$user instanceof User) {
                            return $qb->andWhere('1 = 0');
                        }

                        return $qb
                            ->leftJoin('page.profilsAcces', 'profilAccesFiltre')
                            ->leftJoin('profilAccesFiltre.users', 'userFiltre')
                            ->andWhere('userFiltre = :utilisateurConnecte')
                            ->setParameter('utilisateurConnecte', $user)
                            ->distinct();
                    },
                ])
                ->setRequired(true)
                ->setHelp('Le bloc sera automatiquement ajouté à cette page.')
                ->setColumns(8);

            yield IntegerField::new('ordreRattachement', 'Ordre dans la page')
                ->setRequired(true)
                ->setHelp('Plus le nombre est petit, plus le bloc apparaît haut dans la page.')
                ->setColumns(4);
        }

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
        $bloc->setOrdreRattachement(1);
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

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Bloc) {
            return;
        }

        $page = $entityInstance->getPageRattachement();

        if (!$page instanceof Page) {
            throw $this->createAccessDeniedException('Tu dois choisir une page pour rattacher ce bloc.');
        }

        if (!$this->isGranted('ROLE_ADMIN') && !$this->userCanEditPage($page)) {
            throw $this->createAccessDeniedException('Tu ne peux pas ajouter un bloc sur cette page.');
        }

        $pageBloc = new PageBloc();
        $pageBloc->setPage($page);
        $pageBloc->setBloc($entityInstance);
        $pageBloc->setOrdre($entityInstance->getOrdreRattachement() ?? 1);

        $entityManager->persist($entityInstance);
        $entityManager->persist($pageBloc);
        $entityManager->flush();
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

        $this->denyAccessUnlessGranted(BlocVoter::DELETE, $bloc);

        return parent::delete($context);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::INDEX, 'ROLE_USER')
            ->setPermission(Action::NEW, 'ROLE_USER')
            ->setPermission(Action::EDIT, 'ROLE_USER')
            ->setPermission(Action::DELETE, 'ROLE_USER')
            ->disable(Action::DETAIL)
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                fn (Action $action) => $action->setLabel('Ajouter un bloc')
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $action) => $action->displayIf(
                    fn (Bloc $bloc): bool => $this->isGranted(BlocVoter::EDIT, $bloc)
                )
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $action) => $action->displayIf(
                    fn (Bloc $bloc): bool => $this->isGranted(BlocVoter::DELETE, $bloc)
                )
            );
    }

    private function userCanEditPage(Page $page): bool
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return false;
        }

        foreach ($user->getProfilsAcces() as $profilAcces) {
            foreach ($profilAcces->getPages() as $pageAutorisee) {
                if ($pageAutorisee->getId() === $page->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}