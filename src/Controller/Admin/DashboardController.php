<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->redirectToRoute('admin_bloc_index');

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Atais Informatique');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkTo(BlocCrudController::class, 'Blocs', 'fa fa-cubes');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
        yield MenuItem::linkTo(MenuCrudController::class, 'Menus', 'fa fa-list');
        yield MenuItem::linkTo(MediaCrudController::class, 'Médias', 'fa fa-images');
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file');
        yield MenuItem::linkTo(ElementMenuCrudController::class, 'Éléments de menu', 'fa fa-list');
        yield MenuItem::linkTo(ContactCrudController::class, 'Contacts', 'fa fa-envelope');
        yield MenuItem::linkTo(ParametreCrudController::class, 'Paramètres', 'fa fa-cogs');

    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin.css');
    }
}
