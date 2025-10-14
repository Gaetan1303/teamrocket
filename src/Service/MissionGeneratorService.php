<?php
namespace App\Service;

class MissionGeneratorService
{
    public function generateFirstMission(string $teamNom): array
    {
        $mission = [];

        switch ($teamNom) {
            case 'Team Rocket':
                $mission = [
                    'titre' => 'Vol du Pokémon Rare',
                    'description' => 'Un dresseur connu sous le nom de "Jeune Kevin" sur la Route 3 vient d\'attraper un *Pokémon très prometteur*. Volez-le et ramenez-le au QG immédiatement !',
                    'cible_type' => 'Dresseur',
                    'action_verbe' => 'Voler',
                ];
                break;

            case 'Team Plasma':
                $mission = [
                    'titre' => 'Libération Forcée',
                    'description' => 'Un Méga-Évolueur garde un Pokémon captif dans une Master Ball. Rendez-vous au Centre Commercial et *libérez* ce Pokémon de son esclavage !',
                    'cible_type' => 'Pokémon',
                    'action_verbe' => 'Libérer',
                ];
                break;

            case 'Team Star':
                $mission = [
                    'titre' => 'Racket de l\'Arène',
                    'description' => 'Un nouveau challenger menace la réputation de notre groupe. Rendez-vous devant l\'Arène locale et *forcez-le à nous donner son meilleur Pokémon* après l\'avoir battu.',
                    'cible_type' => 'Dresseur/Racket',
                    'action_verbe' => 'Intimider/Gagner',
                ];
                break;

            default:
                $mission = [
                    'titre' => 'Mission Standard (Défaut)',
                    'description' => 'Volez le Pokémon le plus faible que vous trouvez. Le Boss a besoin de Magicarpe.',
                    'cible_type' => 'Pokémon',
                    'action_verbe' => 'Voler',
                ];
                break;
        }

        return $mission;
    }
}