<?php

namespace App\DataFixtures;

use App\Entity\TeamVilain;
use App\Entity\Sbire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        /****************  1. ÉQUIPES  ****************/
        $teamsData = [
            ['name' => 'Les Légions Rouges',   'region' => 'Europe de l\'Est', 'credo' => 'Ordre par la force',        'color' => '#B71C1C'],
            ['name' => 'Shadow Syndicate',     'region' => 'Asie du Sud-Est',  'credo' => 'Discrétion et profit',      'color' => '#1A237E'],
            ['name' => 'Cyber Maraudeurs',     'region' => 'Amérique du Nord', 'credo' => 'Chaos numérique',           'color' => '#FF6F00'],
        ];

        $teams = [];
        foreach ($teamsData as $t) {
            $team = new TeamVilain();
            $team->setName($t['name'])
                 ->setRegion($t['region'])
                 ->setCredo($t['credo'])
                 ->setColorCode($t['color']);
            $manager->persist($team);
            $teams[] = $team;   // on garde la référence
        }

        /****************  2. SBIRES  ****************/
        $sbiresData = [
            ['email' => 'ivan@rouge.fr',      'codename' => 'RedWolf',   'roles' => ['ROLE_ADMIN']],
            ['email' => 'lisa@shadow.sg',     'codename' => 'Kuro',      'roles' => ['ROLE_USER']],
            ['email' => 'bob@cyber.us',       'codename' => 'ZeroByte',  'roles' => ['ROLE_USER']],
            ['email' => 'nina@rouge.fr',      'codename' => 'Babayaga',  'roles' => ['ROLE_USER']],
            ['email' => 'ken@shadow.sg',      'codename' => 'Oni',       'roles' => ['ROLE_USER']],
            ['email' => 'alice@cyber.us',     'codename' => 'Glitch',    'roles' => ['ROLE_USER']],
            ['email' => 'dmitri@rouge.fr',    'codename' => 'Boris',     'roles' => ['ROLE_USER']],
            ['email' => 'mei@shadow.sg',      'codename' => 'Silencer',  'roles' => ['ROLE_USER']],
            ['email' => 'charlie@cyber.us',   'codename' => '404',       'roles' => ['ROLE_USER']],
            ['email' => 'zoe@cyber.us',       'codename' => 'Hexa',      'roles' => ['ROLE_USER']],
        ];

        foreach ($sbiresData as $s) {
            $sbire = new Sbire();
            $sbire->setEmail($s['email'])
                  ->setCodename($s['codename'])
                  ->setRoles($s['roles'])
                  ->setUuid(Uuid::v4()->toRfc4122());

            // mot de passe = "password"
            $sbire->setPassword(
                $this->hasher->hashPassword($sbire, 'password')
            );

            // assignation aléatoire à une équipe
            $sbire->setTeamVilain($teams[array_rand($teams)]);

            $manager->persist($sbire);
        }

        $manager->flush();
    }
}