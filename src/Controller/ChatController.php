<?php
namespace App\Controller;

use App\Service\MercureJwtFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'chat_index')]
    public function index(MercureJwtFactory $jwtFactory): Response
    {
        $subscriberJwt = $jwtFactory->createSubscriberJwt();

        return $this->render('chat/index.html.twig', [
            'mercure_jwt' => $subscriberJwt,
            'mercure_url' => $_ENV['MERCURE_PUBLIC_URL'] ?? 'http://localhost:9080/.well-known/mercure',
        ]);
    }
}
