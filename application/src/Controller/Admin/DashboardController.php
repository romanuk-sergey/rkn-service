<?php

namespace App\Controller\Admin;

use App\Entity\Email;
use App\Entity\Log;
use App\Entity\RknResponse;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }

    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class)
            ->setPermission(User::ROLE_ADMIN);
        yield MenuItem::linkToRoute('Download request files', 'fa fa-download','download_request')
            ->setPermission(User::ROLE_ADMIN);
        yield MenuItem::linkToCrud('RknResponses', 'fas fa-reply', RknResponse::class);
        yield MenuItem::linkToCrud('Log', 'fas fa-clipboard', Log::class);
        yield MenuItem::linkToCrud('Уведомления', 'far fa-envelope', Email::class);
        yield MenuItem::linkToUrl('Social actual list', 'fas fa-list-alt',
            $this->adminUrlGenerator
                ->setController(RknResponseCrudController::class)
                ->setAction('actualSocialList')
                ->generateUrl()
        );
        yield MenuItem::linkToUrl('Private key', 'fa fa-key',
            $this->adminUrlGenerator
                ->setController(PrivateKeyController::class)
                ->setAction('privateKey')
                ->generateUrl()
        );
    }
}
