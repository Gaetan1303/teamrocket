<?php

namespace App\Controller;

use App\Entity\Sbire;
use App\Service\PokemonApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class GameController extends AbstractController
{
    /* -----------------------------------------------------------------
     * Page du mini-jeu
     * ----------------------------------------------------------------- */
    #[Route('/game/first-theft', name: 'game_first_theft')]
    #[IsGranted('ROLE_USER')]
    public function firstTheft(CsrfTokenManagerInterface $csrfTokenManager): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('game/first_theft.html.twig', [
            'csrf_token' => $csrfTokenManager->getToken('first_theft')->getValue(),
        ]);
    }

    /* -----------------------------------------------------------------
     * Réception du résultat (Ajax)
     * ----------------------------------------------------------------- */
    #[Route('/game/first-theft/result', name: 'game_first_theft_result', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function result(
        Request $request,
        EntityManagerInterface $em,
        PokemonApiService $pokemonApi,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof Sbire) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur invalide'], 400);
        }

        // Vérification CSRF
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManager->isTokenValid($csrfTokenManager->getToken('first_theft'), $token)) {
            return new JsonResponse(['success' => false, 'message' => 'Token CSRF invalide'], 400);
        }

        $data      = json_decode($request->getContent(), true);
        $success   = isset($data['success']) && $data['success'] === true;
        $starter   = $data['starter'] ?? null;
        $starterId = $data['starterId'] ?? null;

        // Succès : on valide et on enregistre
        if ($success && $starter && $starterId) {
            if (!$pokemonApi->exists($starterId)) {
                return new JsonResponse(['success' => false, 'message' => 'Pokémon invalide'], 400);
            }

            $user->setHasDoneFirstTheft(true);
            $user->setStarterPokemon($starter);
            $user->setStarterPokemonId($starterId);

            $em->persist($user);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'message' => "Premier vol réussi : tu as attrapé $starter !",
                'sprite'  => $pokemonApi->getSprite($starterId),
                'types'   => $pokemonApi->getTypes($starterId),
            ]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Échec du vol ou données manquantes'], 200);
    }

    /* -----------------------------------------------------------------
     * Profil du sbire
     * ----------------------------------------------------------------- */
    #[Route('/profile', name: 'sbire_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(PokemonApiService $pokemonApi): \Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();
        if (!$user instanceof Sbire) {
            throw $this->createAccessDeniedException();
        }

        $pokemonData = null;
        if ($user->getStarterPokemonId()) {
            $pokemonData = [
                'name'   => $user->getStarterPokemon(),
                'sprite' => $pokemonApi->getSprite($user->getStarterPokemonId()),
                'types'  => $pokemonApi->getTypes($user->getStarterPokemonId()),
            ];
        }

        return $this->render('game/profile.html.twig', [
            'sbire'   => $user,
            'pokemon' => $pokemonData,
        ]);
    }
}