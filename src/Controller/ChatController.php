<?php

namespace App\Controller;

use App\Entity\Chat;
use App\Repository\TeamVilainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat/{teamId?}', name: 'app_chat', defaults: ['teamId' => null], requirements: ['teamId' => '\\d+'])]
    public function index(?int $teamId): JsonResponse
    {
        return $this->json(['teamId' => $teamId]);
    }

    #[Route('/chat/send', name: 'app_chat_send', methods: ['POST'])]
    public function send(
        Request $request,
        HubInterface $hub,
        EntityManagerInterface $em,
        TeamVilainRepository $teamRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $teamId = $data['teamId'] ?? null;
        $message = trim($data['message'] ?? '');

        if (!$message) {
            return $this->json(['error' => 'Empty message'], 400);
        }

        $team = $teamId ? $teamRepo->find($teamId) : null;

        $chat = (new Chat())
            ->setTeam($team)
            ->setAuthor($this->getUser()->getCodename()) // Sbire
            ->setMessage($message)
            ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($chat);
        $em->flush();

        // Mercure topic
        $topic = $team
            ? 'urn:teamrocket:chat:team/' . $team->getId()
            : 'urn:teamrocket:chat:global';

        // Publishing to Mercure is now handled by the Doctrine listener -> event -> SendChatMercureListener
        // This keeps concerns separated: controller persists the Chat and the listener publishes it.

        return $this->json(['status' => 'published']);
    }
}