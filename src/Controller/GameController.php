<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use App\Service\MissionGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    // --- Route spécifique : récupérer un Pokémon par type ---
    #[Route(
        '/game/first-theft/mission-pokemon/{type}',
        name: 'game_mission_pokemon',
        priority: 30
    )]
    public function missionPokemon(string $type, PokemonRepository $repo): JsonResponse
    {
        $normalizedType = ucfirst(strtolower($type));
        $candidates = [];

        foreach ($repo->findAll() as $p) {
            $typesLower = array_map('strtolower', $p->getTypes());
            if (in_array(strtolower($normalizedType), $typesLower, true)) {
                $candidates[] = $p;
            }
        }

        if (!$candidates) {
            return new JsonResponse(['error' => "Aucun Pokémon trouvé pour le type $normalizedType"], 404);
        }

        $pokemon = $candidates[array_rand($candidates)];

        $isShiny = method_exists($pokemon, 'isShiny') ? $pokemon->isShiny() : false;
        $sprite  = null;

        if ($isShiny && method_exists($pokemon, 'getSpriteFrontShiny')) {
            $sprite = $pokemon->getSpriteFrontShiny();
        } elseif (method_exists($pokemon, 'getSpriteFront')) {
            $sprite = $pokemon->getSpriteFront();
        }

        if (!$sprite) {
            $sprite = '/images/default_pokemon.png';
        }

        return new JsonResponse([
            'id'     => $pokemon->getId(),
            'name'   => $pokemon->getName(),
            'types'  => $pokemon->getTypes(),
            'sprite' => $sprite,
            'shiny'  => $isShiny,
        ]);
    }

    // --- Route POST pour la capture ---
    #[Route(
        '/game/first-theft/result',
        name: 'game_first_theft_result',
        methods: ['POST'],
        priority: 40
    )]
    public function firstTheftResult(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides'], 400);
        }

        return new JsonResponse([
            'success' => true,
            'message' => sprintf('Tu as capturé %s avec succès !', $data['starter'] ?? 'un Pokémon mystérieux'),
        ]);
    }

    // --- Route générique : mission pour une team ---
    #[Route(
        '/game/first-theft/{team}',
        name: 'game_first_theft',
        defaults: ['team' => null],
        priority: 10
    )]
    public function firstTheft(?string $team, MissionGeneratorService $missionGen): Response
    {
        if (!$team) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $mission = $missionGen->generateForTeam($team);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Team inconnue');
        }

        $csrf = $this->container->get('security.csrf.token_manager')->getToken('game_first_theft');

        return $this->render('game/first_theft.html.twig', [
            'mission' => (object)[
                'titre'        => "Mission pour {$mission['team']}",
                'description'  => $mission['goal'],
                'action_verbe' => 'Capturer',
                'cible_type'   => $mission['target_type'],
                'team_color'   => $mission['color'],
                'region'       => $mission['region'],
            ],
            'csrf_token' => $csrf,
        ]);
    }
}
