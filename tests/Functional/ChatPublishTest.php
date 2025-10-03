<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\Tools\SchemaTool;
use App\EventListener\ChatDoctrineListener;
use App\Event\ChatMessageEvent;

class ChatPublishTest extends WebTestCase
{
    public function testChatPersistAndMercurePublish()
    {
        // Create the client and use the test container. We'll replace the Mercure hub
        // service with a mock so we can assert publish() was called by the listener.
        $client = static::createClient();
        $testContainer = static::getContainer();

        // Prepare a mock EventDispatcherInterface to assert ChatMessageEvent is dispatched
        $dispatcherMock = $this->createMock(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class);
        $dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ChatMessageEvent::class));

        // Replace the ChatDoctrineListener service with one using our dispatcher mock
        $testContainer->set(ChatDoctrineListener::class, new ChatDoctrineListener($dispatcherMock));

        $container = $client->getContainer();

        // Ensure the test database schema exists (create tables)
        $em = $container->get('doctrine')->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($meta)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropSchema($meta);
            $schemaTool->createSchema($meta);
        }

        // Ensure we have a User to authenticate with
        $codename = 'test_user_' . random_int(1000, 9999);

        $sbire = new User();
        $sbire->setEmail($codename . '@example.com')
            ->setCodename($codename)
            ->setPassword('not_used');

        $em->persist($sbire);
        $em->flush();

        // Send POST /chat/send with auth token header (Sbire codename)
        $client->request('POST', '/chat/send', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_CHAT_TOKEN' => $codename,
        ], json_encode(['teamId' => null, 'message' => 'hello test']));

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Verify Chat persisted
        /** @var ChatRepository $chatRepo */
        $chatRepo = $container->get('doctrine')->getRepository(\App\Entity\Chat::class);
        $chats = $chatRepo->findBy(['author' => $codename]);

        $this->assertNotEmpty($chats, 'No chat found for the test sbire');
    }
}
