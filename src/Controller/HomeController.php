<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PokemonApiService;
use Psr\Log\LoggerInterface;
use App\Repository\TeamVilainRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PokemonApiService $pokemonApiService, LoggerInterface $logger, TeamVilainRepository $teamRepo): Response
    {
        // $teams = $teamRepo->findAll();

        // // Transform teams into a simple array for templates (avoid serializing entities)
        // $teamData = array_map(function($t){
        //     return [
        //         'id' => $t->getId(),
        //         'name' => method_exists($t, 'getName') ? $t->getName() : (string) $t->getId(),
        //     ];
        // }, $teams);
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
            // 'teams' => $teams,
            // 'teamData' => $teamData,
        ]);
    }

    #[Route('/about', name: 'app_home_about')]
    public function about(): Response
    {
        return new Response("about");
    }
}
