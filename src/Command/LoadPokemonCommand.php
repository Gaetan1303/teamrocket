<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Service\PokemonApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:pokemon:load',
    description: 'Charge tous les Pokémon depuis l’API externe dans la base locale'
)]
class LoadPokemonCommand extends Command
{
    public function __construct(
        private PokemonApiService $api,
        private EntityManagerInterface $em,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Chargement des Pokémon');

        $max = $this->params->get('pokeapi_max_id'); // 1025 dans services.yaml
        for ($i = 1; $i <= $max; $i++) {
            $dto = $this->api->getPokemon($i);
            if (!$dto) {
                $io->warning("Pokemon $i absent de l’API");
                continue;
            }

            $pokemon = $this->em->getRepository(Pokemon::class)->findOneBy(['apiId' => (string)$dto['id']])
                ?? new Pokemon();

            $pokemon
                ->setApiId((string)$dto['id'])
                ->setName($dto['name'])
                ->setSpriteFront($dto['sprite'] ?? null)
                ->setTypes(array_map(fn($t) => $t['name'], $dto['apiTypes'] ?? []))
                ->setHp($dto['stats']['HP'])
                ->setAttack($dto['stats']['attack'])
                ->setDefense($dto['stats']['defense'])
                ->setSpecialAttack($dto['stats']['special_attack'])
                ->setSpecialDefense($dto['stats']['special_defense'])
                ->setSpeed($dto['stats']['speed'])
                ->setStatus(Pokemon::STATUS_WILD);

            $this->em->persist($pokemon);
            $io->text("{$dto['name']} chargé");
        }

        $this->em->flush();
        $io->success('Tous les Pokémon ont été chargés !');

        return Command::SUCCESS;
    }
}