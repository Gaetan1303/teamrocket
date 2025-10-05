<?php

namespace App\Command;

use App\Entity\Chat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsCommand(name: 'app:simulate-chat', description: 'Simulate chat messages to test Mercure and the chat pipeline')]
class SimulateChatCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private ?HubInterface $hub = null,
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%mercure.jwt.key%')]
        private string $publisherJwt = ''
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, 'Number of messages to send', 10)
            ->addOption('team', null, InputOption::VALUE_OPTIONAL, 'TeamVilain id to send messages to', null)
            ->addOption('direct', null, InputOption::VALUE_NONE, 'Publish directly to Mercure instead of persisting');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int) $input->getArgument('count');
        $teamId = $input->getOption('team');
        $direct = (bool) $input->getOption('direct');

        $repoSbire = $this->em->getRepository(User::class);

        // Ensure there's at least one sbire to use
        $sbires = $repoSbire->findBy([], null, 5);
        if (empty($sbires)) {
            for ($i = 1; $i <= 3; $i++) {
                $s = new User();
                $s->setEmail("sim{$i}@example.com");
                $s->setCodename("sim{$i}");
                $s->setPassword('not_used');
                $this->em->persist($s);
            }
            $this->em->flush();
            $sbires = $repoSbire->findBy([], null, 5);
        }

        $output->writeln("Simulating $count messages (direct publish: " . ($direct ? 'yes' : 'no') . ")");

        for ($i = 0; $i < $count; $i++) {
            $author = $sbires[array_rand($sbires)];
            $message = sprintf('Simulated message #%d from %s', $i + 1, $author->getCodename());

            if ($direct) {
                // Topic dynamique
                $topic = $teamId ? 'urn:teamrocket:chat:team/' . $teamId : 'urn:teamrocket:chat:global';
                
                // Payload harmonisé avec SendChatMercureListener.php
                $now = new \DateTimeImmutable();
                $data = [
                    'id' => 'sim-' . uniqid(),
                    'user' => $author->getCodename(), // CLÉ HARMONISÉE
                    'message' => $message,
                    'createdAt' => $now->format('Y-m-d H:i:s'), // CLÉ ET FORMAT HARMONISÉS
                ];
                
                $update = [
                    'topic' => $topic,
                    'data' => json_encode($data),
                ];
                
                $ch = curl_init('http://localhost:9080/.well-known/mercure');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($update));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $this->publisherJwt,
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode === 200) {
                    $output->writeln("Published direct to $topic: $message");
                } else {
                    $output->writeln("Failed to publish direct to $topic: $message (HTTP $httpCode) Response: $response");
                }
            } else {
                // persist a Chat so doctrine listener dispatches and the normal pipeline runs
                $chat = new Chat();
                if ($teamId) {
                    // Importe l'entité TeamVilain
                    $team = $this->em->getRepository(\App\Entity\TeamVilain::class)->find($teamId);
                    if ($team) $chat->setTeam($team);
                }
                // NOTE: Dans le flux normal, l'auteur devrait être un objet User. 
                // Ici, on utilise getCodename() pour la simulation, ce qui est cohérent 
                // avec votre logique de test mais à adapter en production.
                $chat->setAuthor($author->getCodename()) 
                    ->setMessage($message)
                    ->setCreatedAt(new \DateTimeImmutable());
                $this->em->persist($chat);
                $this->em->flush();
                $output->writeln("Persisted Chat #{$chat->getId()} by {$author->getCodename()}");
            }

            // small throttle
            usleep(100000);
        }

        $output->writeln('Done');
        return Command::SUCCESS;
    }
}