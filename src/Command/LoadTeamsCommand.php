<?php

namespace App\Command;

use App\Entity\TeamVilain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:load-teams', description: 'Injecte la liste des teams Pokémon')]
class LoadTeamsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $io = new SymfonyStyle($in, $out);
    $rows = [
    ['Team Rocket',           'Kanto/Johto',  'Voler les Pokémon pour dominer le monde',                '#8B3A3A', 'R'],
    ['Team Aqua',             'Hoenn',        'Réveiller Kyogre et inonder la planète',                 '#2563EB', 'A'],
    ['Team Magma',            'Hoenn',        'Réveiller Groudon et augmenter les terres émergées',     '#DC2626', 'M'],
    ['Team Galaxie',          'Sinnoh',       'Créer un nouvel univers en capturant Dialga/Palkia',     '#4B0082', 'G'],
    ['Team Plasma',           'Unys',         'Libérer les Pokémon de leurs dresseurs',                 '#FFFFFF', 'P'],
    ['Team Flare',            'Kalos',        'Créer un monde plus beau… en éliminant les faibles',     '#FF4500', 'F'],
    ['Team Skull',            'Alola',        'Faire le chaos et voler les Pokémon',                    '#6B7280', 'S'],
    ['Fondation Æther',       'Alola',        'Protéger les Pokémon… mais parfois au prix de la liberté','#10B981', 'Æ'],
    ['Team Rainbow Rocket',   'Multivers',    'Réunir toutes les teams pour dominer les dimensions',    '#000000', 'RR'],
    ['Team Star',             'Paldea',       'Faire la fête et éviter les cours',                      '#F59E0B', 'STAR'],
];

        $io->title('Chargement des teams Pokémon');
        $repo = $this->em->getRepository(TeamVilain::class);

     foreach ($rows as [$name, $region, $credo, $color, $code]) {
    if ($repo->findOneBy(['name' => $name])) {
        $io->text("  <comment>Ignoré</comment> : $name existe déjà.");
        continue;
    }
    $t = (new TeamVilain())
        ->setName($name)
        ->setRegion($region)
        ->setCredo($credo)
        ->setColorCode($color)
        ->setCode($code);   
    $this->em->persist($t);
    $io->text("  <info>Ajout</info> : $name");
}
        $this->em->flush();
        $io->success('Terminé !');
        return Command::SUCCESS;
    }
}