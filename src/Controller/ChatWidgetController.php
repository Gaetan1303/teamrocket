<?php
namespace App\Controller;

use App\Repository\ChannelRepository;
use App\Service\MercureJwtFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatWidgetController extends AbstractController
{
    #[Route('/chatwidget', name: 'chat_widget')]
    public function widget(
        ChannelRepository $channelRepo,
        MercureJwtFactory $jwtFactory
    ): Response {
        return $this->render('chatwidget.html.twig', [
            'channels'    => $channelRepo->findAll(),
            'mercure_url' => $_ENV['MERCURE_PUBLIC_URL'] ?? 'https://localhost/.well-known/mercure',
            'mercure_jwt' => $jwtFactory->createSubscriberJwt(),
        ]);
    }
}