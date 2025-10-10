<?php
namespace App\Controller;

use App\Entity\Chat;
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
    #[Route('/chat', name: 'chat_index', methods: ['GET'])]
    public function index(MercureJwtFactory $jwtFactory): Response
    {
        return $this->render('chat/index.html.twig', [
            'mercure_jwt' => $jwtFactory->createSubscriberJwt(),
            'mercure_url' => $_ENV['MERCURE_PUBLIC_URL'] ?? 'https://localhost/.well-known/mercure',
        ]);
    }

    #[Route('/chat/history/{channel}', name: 'chat_history', methods: ['GET'])]
    public function history(string $channel, EntityManagerInterface $em): JsonResponse
    {
        $messages = $em->createQueryBuilder()
            ->select('c.id, c.message, c.createdAt, u.username as user')
            ->from(Chat::class, 'c')
            ->join('c.user', 'u')
            ->join('c.channel', 'ch')
            ->where('ch.slug = :slug')
            ->setParameter('slug', $channel)
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
        EntityManagerInterface $em,
        ChannelRepository $channelRepo
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

        $chat = new Chat();
        $chat->setMessage($content);
        $chat->setCreatedAt(new \DateTimeImmutable());
        $chat->setUser($this->getUser());
        $chat->setChannel($ch);

        $em->persist($chat);
        $em->flush();

        $hub->publish(new Update(
            "urn:teamrocket:chat:{$channel}",
            json_encode([
                'id'        => $chat->getId(),
                'user'      => $this->getUser()?->getUserIdentifier() ?? 'Anonyme',
                'message'   => $chat->getMessage(),
                'createdAt' => $chat->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
        ));

        return new JsonResponse(['status' => 'published']);
    }

    #[Route('/chat/clear', name: 'chat_clear', methods: ['DELETE'])]
    public function clear(EntityManagerInterface $em): JsonResponse
    {
        $em->createQuery('DELETE FROM App\Entity\Chat')->execute();
        return new JsonResponse(['status' => 'cleared']);
    }
}