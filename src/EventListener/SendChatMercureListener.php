<?php
namespace App\EventListener;

use App\Event\ChatMessageEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class SendChatMercureListener
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function onChatMessage(ChatMessageEvent $event): void
    {
        // Récupération du message envoyé
        $chat = $event->getChat();

        // Définition du topic (canal Mercure)
        $topic = 'urn:teamrocket:chat:global';

        // Construction du payload JSON
        $payload = [
            'id' => $chat->getId(),
            'user' => $chat->getUser()->getUsername(),
            'message' => $chat->getMessage(),
            'createdAt' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        // Publication sur Mercure
        $update = new Update(
            $topic,
            json_encode($payload, JSON_THROW_ON_ERROR)
        );

        $this->hub->publish($update);
    }
}
