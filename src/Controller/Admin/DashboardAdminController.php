<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\CompteCourant;
use App\Entity\CompteAction;
use App\Entity\Operation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardAdminController extends AbstractDashboardController
{
    // #[Route('/admin', name: 'admin')]
    // public function index(): Response
    // {
    //     return parent::index();

    //     // Option 1. You can make your dashboard redirect to some common page of your backend
    //     //
    //     // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
    //     // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

    //     // Option 2. You can make your dashboard redirect to different pages depending on the user
    //     //
    //     // if ('jane' === $this->getUser()->getUsername()) {
    //     //     return $this->redirect('...');
    //     // }

    //     // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
    //     // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
    //     //
    //     // return $this->render('some/path/my-dashboard.html.twig');
    // }

    public function __construct(
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    // ... you'll also need to load some CSS/JavaScript assets to render
    // the charts; this is explained later in the chapter about Design

    #[Route('/admin', name: 'showAdmin')]
    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        // ...set chart data and options somehow

        return $this->render('/admin', [
            'chart' => $chart,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Kotkot Bank');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Retour à l\'accueil', 'fa fa-home', 'home'),
            MenuItem::section('Clients'),
            MenuItem::subMenu('Clients', 'fas fa-users', User::class)->setSubItems([
                MenuItem::linkToCrud('Afficher les clients', 'fas fa-eye', User::class)
            ]),
            MenuItem::linkToCrud('Comptes Courants', 'fas fa-comptesCourants', CompteCourant::class),
            MenuItem::linkToCrud('Operations', 'fas fa-operations', Operation::class),
            MenuItem::linkToCrud('Comptes Action', 'fas fa-comptesAction', CompteAction::class),
            MenuItem::linkToLogout('Déconnexion', 'fa fa-exit'),
        ];
    }
}
