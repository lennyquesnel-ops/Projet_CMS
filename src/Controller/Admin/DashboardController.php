<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_bloc_index');
        }

        return $this->redirectToRoute('admin_page_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Atais Informatique');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file')
            ->setPermission('ROLE_USER');

        yield MenuItem::linkTo(BlocCrudController::class, 'Blocs', 'fa fa-cubes')
            ->setPermission('ROLE_USER');

        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(ProfilAccesCrudController::class, 'Profils d’accès', 'fa fa-user-lock')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(MenuCrudController::class, 'Menus', 'fa fa-list')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(MediaCrudController::class, 'Médias', 'fa fa-images')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(ElementMenuCrudController::class, 'Éléments de menu', 'fa fa-list')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(ContactCrudController::class, 'Contacts', 'fa fa-envelope')
            ->setPermission('ROLE_ADMIN');

        yield MenuItem::linkTo(ParametreCrudController::class, 'Paramètres', 'fa fa-cogs')
            ->setPermission('ROLE_ADMIN');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin.css');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->addFormTheme('admin/form/parametre_valeur_widget.html.twig')
            ->renderContentMaximized()
            ->showEntityActionsInlined();
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                fn (Action $action) => $action
                    ->setLabel('Modifier')
                    ->setIcon('fa fa-pen')
                    ->setHtmlAttributes(['title' => 'Modifier'])
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                fn (Action $action) => $action
                    ->setLabel('Supprimer')
                    ->setIcon('fa fa-trash')
                    ->setHtmlAttributes(['title' => 'Supprimer'])
            );
    }
}