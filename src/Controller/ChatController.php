<?php
namespace App\Controller;

use App\Repository\ChannelRepository;
use App\Service\MercureJwtFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ChatController extends AbstractController
{
    #[Route('/chat', name: 'chat_index')]
    public function index(MercureJwtFactory $jwtFactory): Response
    {
        return $this->render('chat/index.html.twig', [
            'mercure_jwt' => $jwtFactory->createSubscriberJwt(),
            'mercure_url' => $_ENV['MERCURE_PUBLIC_URL'] ?? 'https://localhost/.well-known/mercure',
        ]);
    }

    #[Route('/chat/history/{channel}', name: 'chat_history', methods: ['GET'])]
    public function history(
        string $channel,
        ChannelRepository $channelRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $ch = $channelRepo->findOneBy(['slug' => $channel]);
        if (!$ch) {
            return new JsonResponse(['error' => 'Canal inconnu'], 404);
        }

        $messages = $em->createQueryBuilder()
            ->select('c.id, c.message, c.createdAt, u.codename as user')
            ->from(\App\Entity\Chat::class, 'c')
            ->join('c.user', 'u')
            ->where('c.channel = :ch')
            ->setParameter('ch', $ch)
            ->orderBy('c.createdAt', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        return new JsonResponse($messages);
    }

    #[Route('/chat/send/{channel}', name: 'chat_send', methods: ['POST'])]
    public function send(
        string $channel,
        Request $request,
        HubInterface $hub,
        ChannelRepository $channelRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $content = trim($data['message'] ?? '');
        if (!$content) {
            return new JsonResponse(['error' => 'Message vide'], 400);
        }

        $ch = $channelRepo->findOneBy(['slug' => $channel]);
        if (!$ch) {
            return new JsonResponse(['error' => 'Canal inconnu'], 404);
        }

        $chat = new \App\Entity\Chat();
        $chat->setMessage($content)
             ->setCreatedAt(new \DateTimeImmutable())
             ->setUser($this->getUser())
             ->setChannel($ch);

        $em->persist($chat);
        $em->flush();

        $hub->publish(new Update(
            "urn:teamrocket:chat:{$channel}",
            json_encode([
                'id'        => $chat->getId(),
                'user'      => $this->getUser()->getUserIdentifier(),
                'message'   => $chat->getMessage(),
                'createdAt' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
        ));

        return new JsonResponse(['status' => 'published']);
    }
}