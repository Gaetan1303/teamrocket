<?php

namespace App\Service;

use App\Repository\PokemonRepository;

class MissionGeneratorService
{
    private PokemonRepository $pokemonRepo;

    public function __construct(PokemonRepository $pokemonRepo)
    {
        $this->pokemonRepo = $pokemonRepo;
    }

    public function generateForTeam(string $teamName): array
    {
        // Table des teams et de leur logique
        $teams = [
            'Team Rocket' => [
                'region' => 'Kanto/Johto',
                'goal' => 'Voler les Pokémon rares pour dominer le monde',
                'color' => '#8B3A3A',
                'type' => 'Normal',
            ],
            'Team Aqua' => [
                'region' => 'Hoenn',
                'goal' => 'Réveiller Kyogre et inonder la planète',
                'color' => '#2563EB',
                'type' => 'Eau',
            ],
            'Team Magma' => [
                'region' => 'Hoenn',
                'goal' => 'Réveiller Groudon et agrandir les continents',
                'color' => '#DC2626',
                'type' => 'Feu',
            ],
            'Team Galaxie' => [
                'region' => 'Sinnoh',
                'goal' => 'Créer un nouvel univers en capturant Dialga/Palkia',
                'color' => '#4B0082',
                'type' => 'Psy',
            ],
            'Team Plasma' => [
                'region' => 'Unys',
                'goal' => 'Libérer les Pokémon de leurs dresseurs',
                'color' => '#FFFFFF',
                'type' => 'Spectre',
            ],
            'Team Flare' => [
                'region' => 'Kalos',
                'goal' => 'Créer un monde parfait en éliminant les faibles',
                'color' => '#FF4500',
                'type' => 'Feu',
            ],
            'Team Skull' => [
                'region' => 'Alola',
                'goal' => 'Faire le chaos et voler les Pokémon',
                'color' => '#6B7280',
                'type' => 'Ténèbres',
            ],
            'Fondation Æther' => [
                'region' => 'Alola',
                'goal' => 'Protéger les Pokémon coûte que coûte',
                'color' => '#10B981',
                'type' => 'Fée',
            ],
            'Team Rainbow Rocket' => [
                'region' => 'Multivers',
                'goal' => 'Réunir toutes les teams pour dominer les dimensions',
                'color' => '#000000',
                'type' => 'Dragon',
            ],
            'Team Star' => [
                'region' => 'Paldea',
                'goal' => 'Faire la fête et éviter les cours',
                'color' => '#F59E0B',
                'type' => 'Acier',
            ],
        ];

        if (!isset($teams[$teamName])) {
            throw new \InvalidArgumentException("Team inconnue : $teamName");
        }

        $team = $teams[$teamName];

        // Sélection d’un Pokémon de base du bon type
        $pokemons = $this->pokemonRepo->findAll();
        $filtered = array_filter($pokemons, function ($p) use ($team) {
            $types = array_map('strtolower', $p->getTypes());
            $type = strtolower($team['type']);

            // Exclure les évolutions (si propriété getEvolutionStage ou getPreEvolution existe)
            if (method_exists($p, 'getEvolutionStage') && $p->getEvolutionStage() > 1) {
                return false;
            }
            if (method_exists($p, 'getPreEvolution') && $p->getPreEvolution() !== null) {
                return false;
            }

            return in_array($type, $types);
        });

        // Pokémon aléatoire de base
        $pokemon = $filtered ? $filtered[array_rand($filtered)] : null;

        return [
            'team' => $teamName,
            'region' => $team['region'],
            'goal' => $team['goal'],
            'color' => $team['color'],
            'target_type' => $team['type'],
            'target_pokemon' => $pokemon ? $pokemon->getName() : 'Mysterymon',
        ];
    }
}
