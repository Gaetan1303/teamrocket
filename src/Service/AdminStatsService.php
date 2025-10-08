<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\{User,TeamVilain,Pokemon,Chat};

class AdminStatsService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function counts(): array
    {
        return [
            'users'    => $this->em->getRepository(User::class)->count([]),
            'teams'    => $this->em->getRepository(TeamVilain::class)->count([]),
            'pokemons' => $this->em->getRepository(Pokemon::class)->count([]),
            'chats'    => $this->em->getRepository(Chat::class)->count([]),
        ];
    }
}