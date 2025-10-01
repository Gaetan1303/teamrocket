<?php

namespace App\EventListener;

use App\Event\ChatMessageEvent;
use App\Repository\SbireRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class SendChatMercureListener
{
    public function __construct(private HubInterface $hub, private SbireRepository $sbireRepository)
    {
    }

    public function onChatMessage(ChatMessageEvent $event): void
    {
        $chat = $event->chat;
        $team = $chat->getTeam();

        $topic = $team
            ? 'urn:teamrocket:chat:team/' . $team->getId()
            : 'urn:teamrocket:chat:global';

        // try to enrich the payload with the Sbire email if available
        $authorCodename = $chat->getAuthor();
        $email = null;
        if ($authorCodename) {
            $sbire = $this->sbireRepository->findOneBy(['codename' => $authorCodename]);
            if ($sbire) {
                $email = $sbire->getEmail();
            }
        }

        $payload = [
            'id'      => $chat->getId(),
            'author'  => $chat->getAuthor(),
            'message' => $chat->getMessage(),
            'time'    => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
            'teamId'  => $team ? $team->getId() : null,
        ];

        if ($email) {
            $payload['email'] = $email;
        }

        $this->hub->publish(new Update(
            $topic,
            json_encode($payload)
        ));
    }
}