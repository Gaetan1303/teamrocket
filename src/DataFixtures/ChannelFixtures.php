<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChannelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $channels = [
            'general' => '# Général',
            'strat'   => '# Stratégie',
            'blabla'  => '# Blabla',
        ];

        foreach ($channels as $slug => $name) {
            $ch = new Channel();
            $ch->setSlug($slug);
            $ch->setName($name);
            $manager->persist($ch);
        }

        $manager->flush();
    }
}