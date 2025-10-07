<?php

namespace App\Controller\Admin;

use App\Entity\Chat;
use App\Entity\Pokemon;
use App\Entity\TeamVilain;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\PokemonRepository;
use App\Repository\TeamVilainRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UserRepository $userRepo,
        private PokemonRepository $pokeRepo,
        private TeamVilainRepository $teamRepo,
        private ChatRepository $chatRepo,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Stats chiffrées
        $stats = [
            'users' => $this->userRepo->count([]),
            'pokemons' => $this->pokeRepo->count([]),
            'teams' => $this->teamRepo->count([]),
            'messages' => $this->chatRepo->count([]),
        ];

        // 5 derniers inscrits
        $lastUsers = $this->userRepo->findBy([], ['id' => 'DESC'], 5);

        // 5 derniers Pokémon capturés
        $lastCaught = $this->pokeRepo->findBy(
            ['status' => Pokemon::STATUS_CAUGHT],
            ['id' => 'DESC'],
            5
        );

        // Répartition par statut (pour Chart.js)
        $statusStats = [
            'Sauvage' => $this->pokeRepo->count(['status' => Pokemon::STATUS_WILD]),
            'Capturé' => $this->pokeRepo->count(['status' => Pokemon::STATUS_CAUGHT]),
            'Volé' => $this->pokeRepo->count(['status' => Pokemon::STATUS_STOLEN]),
        ];

        // Liens rapides
        $newUserUrl = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->setAction(Action::NEW)
            ->generateUrl();

        $newPokemonUrl = $this->adminUrlGenerator
            ->setController(PokemonCrudController::class)
            ->setAction(Action::NEW)
            ->generateUrl();

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'lastUsers' => $lastUsers,
            'lastCaught' => $lastCaught,
            'statusStats' => $statusStats,
            'newUserUrl' => $newUserUrl,
            'newPokemonUrl' => $newPokemonUrl,
        ]);
        // return $this->redirectToRoute('admin_chat_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TeamRocket Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Gestion');
        yield MenuItem::linkToCrud('Pokémon', 'fa fa-dragon', Pokemon::class);
        yield MenuItem::linkToCrud('Teams de Vilains', 'fa fa-user-ninja', TeamVilain::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Chats', 'fa fa-comments', Chat::class);
    }

 
}