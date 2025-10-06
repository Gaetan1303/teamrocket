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
        if (null !== $chat->getTeam()) {
            // Topic spécifique à l'équipe
            // Ceci suppose que $chat->getTeam() renvoie un objet avec getId() (ex: TeamVilain)
            $topic = 'urn:teamrocket:chat:team/' . $chat->getTeam()->getId();
        } else {
            // Topic global par défaut
            $topic = 'urn:teamrocket:chat:global';
        }

        // Construction du payload JSON
        $payload = [
            'id' => $chat->getId(),
            // On utilise getUserIdentifier() ou getUsername() en fonction de l'implémentation de votre User
            'user' => $chat->getUser()->getUserIdentifier(), 
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