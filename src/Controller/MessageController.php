<?php
namespace App\Controller;

use App\Entity\Chat;
use App\Event\ChatMessageEvent;
use App\Repository\ChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'chat_messages', methods: ['GET'])]
    public function index(ChatRepository $chatRepository): Response
    {
        // On récupère les 50 derniers messages
        $messages = $chatRepository->findLatest(50);

        return $this->render('chat/messages.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/send', name: 'chat_send_message', methods: ['POST'])]
    public function send(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $content = trim($request->request->get('message', ''));
        if ($content === '') {
            return $this->json(['error' => 'Empty message'], 400);
        }

        // Création du message
        $chat = new Chat();
        $chat->setUser($user);
        $chat->setMessage($content);

        // Persistance
        $em->persist($chat);
        $em->flush();

        // Dispatch de l'événement → Mercure notifiera tous les abonnés
        $dispatcher->dispatch(new ChatMessageEvent($chat), ChatMessageEvent::NAME);

        return $this->json([
            'id' => $chat->getId(),
            'user' => $user->getUserIdentifier(),
            'message' => $chat->getMessage(),
            'createdAt' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
