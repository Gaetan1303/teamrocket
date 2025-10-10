<?php

namespace App\Controller;

use App\Repository\ChannelRepository;
use App\Repository\TeamVilainRepository;
use App\Service\MercureJwtFactory;
use App\Service\PokemonApiService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        PokemonApiService $pokemonApiService,
        TeamVilainRepository $teamRepo,
        ChannelRepository $channelRepo,
        MercureJwtFactory $jwtFactory,
        LoggerInterface $logger
    ): Response {
        /* ----------  Teams ---------- */
        $teams = $teamRepo->findAll();

        /* ----------  Pokémon cible ALÉATOIRE ---------- */
        $targetPokemon = null;
        try {
            // ID aléatoire entre 1 et 898
            $randomId = random_int(1, 898);
            $data     = $pokemonApiService->getPokemon($randomId);

            if ($data) {
                $targetPokemon = [
                    'name'   => $data['name'],
                    'sprite' => $data['image'],
                    'types'  => array_column($data['apiTypes'] ?? [], 'name'),
                ];
            }
        } catch (\Throwable $e) {
            $logger->warning('Impossible de récupérer le Pokémon cible : '.$e->getMessage());
        }

        /* ----------  Rendu ---------- */
        return $this->render('home/index.html.twig', [
            'teams'         => $teams,
            'targetPokemon' => $targetPokemon,
            'channels'      => $channelRepo->findAll(),
            'mercure_url'   => $_ENV['MERCURE_PUBLIC_URL'] ?? 'https://localhost/.well-known/mercure',
            'mercure_jwt'   => $jwtFactory->createSubscriberJwt(),
        ]);
    }

    #[Route('/about', name: 'app_home_about')]
    public function about(): Response
    {
        return new Response('about');
    }
}