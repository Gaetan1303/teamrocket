<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\TeamVilain;
use App\Entity\Chat; 
use App\Entity\Pokemon;


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
        // Redirige vers la page d'accueil de l'administration ou la liste des utilisateurs par défaut.
        return parent::index();
        // Alternative : Rediriger vers un CrudController spécifique
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Teamrocket') // Le titre affiché dans le navigateur et l'entête
            ->renderContentMaximized(); // Optionnel : pour maximiser l'espace de contenu
    }

    /**
     * Cette méthode définit les liens de navigation (menu) qui apparaissent dans la barre latérale.
     */
    public function configureMenuItems(): iterable
    {
        // 1. Lien principal vers le Dashboard
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // 2. Séparateur pour les entités (optionnel)
        yield MenuItem::section('Gestion des Vilains', 'fa fa-user-secret');

        // 3. Liens vers les CRUD Controllers
        // Les Sbires (Utilisateurs)
        yield MenuItem::linkToCrud('Sbires (Users)', 'fas fa-users', User::class)
             ->setController(\App\Controller\Admin\UserCrudController::class); // Spécifier explicitement le CrudController

        // Les Teams de Vilains
        yield MenuItem::linkToCrud('Teams Vilains', 'fas fa-shield-alt', TeamVilain::class)
             ->setController(\App\Controller\Admin\TeamVilainCrudController::class);

        // 4. Autre Sections
        yield MenuItem::section('Contenu & Divers', 'fa fa-list');

        // Pokemon (si c'est une entité gérée)
        yield MenuItem::linkToCrud('Pokémon', 'fas fa-paw', Pokemon::class)
             ->setController(\App\Controller\Admin\PokemonCrudController::class);

        // Chat (si c'est une entité gérée)
        yield MenuItem::linkToCrud('Chats', 'fas fa-comments', Chat::class)
             ->setController(\App\Controller\Admin\ChatCrudController::class);

       
    }
}