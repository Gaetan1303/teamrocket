<?php

namespace App\Command;

use App\Entity\Chat;
use App\Entity\Sbire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mercure\HubInterface;

#[AsCommand(name: 'app:simulate-chat', description: 'Simulate chat messages to test Mercure and the chat pipeline')]
class SimulateChatCommand extends Command
{
    public function __construct(private EntityManagerInterface $em, private ?HubInterface $hub = null)
    {
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

        $repoSbire = $this->em->getRepository(Sbire::class);

        // Ensure there's at least one sbire to use
        $sbires = $repoSbire->findBy([], null, 5);
        if (empty($sbires)) {
            // create some dummy sbires
            for ($i = 1; $i <= 3; $i++) {
                $s = new Sbire();
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

            if ($direct && $this->hub) {
                // publish directly
                $topic = $teamId ? 'urn:teamrocket:chat:team/' . $teamId : 'urn:teamrocket:chat:global';
                $this->hub->publish(new \Symfony\Component\Mercure\Update($topic, json_encode([
                    'id' => 'sim-' . uniqid(),
                    'author' => $author->getCodename(),
                    'message' => $message,
                    'time' => (new \DateTimeImmutable())->format('H:i'),
                ])));
                $output->writeln("Published direct to $topic: $message");
            } else {
                // persist a Chat so doctrine listener dispatches and the normal pipeline runs
                $chat = new Chat();
                if ($teamId) {
                    $team = $this->em->getRepository(\App\Entity\TeamVilain::class)->find($teamId);
                    if ($team) $chat->setTeam($team);
                }
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
