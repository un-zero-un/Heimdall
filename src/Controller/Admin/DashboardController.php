<?php

namespace App\Controller\Admin;

use App\Model\ConfiguredCheck;
use App\Model\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $urlGenerator)
    {
    }
    
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Heimdall');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Site', 'fas fa-folder-open', Site::class);
        yield MenuItem::linkToCrud('ConfiguredCheck', 'fas fa-folder-open', ConfiguredCheck::class);
    }

    #[Route(path: '/admin', priority: 1000)]
    public function index(): Response
    {
        return $this->redirect($this->urlGenerator->setController(SiteCrudController::class)->generateUrl());
    }
}
