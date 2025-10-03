<?php

namespace App\Tests\Unit;

use App\Entity\Chat;
use App\Event\ChatMessageEvent;
use App\EventListener\SendChatMercureListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class SendChatMercureListenerTest extends TestCase
{
    public function testOnChatMessagePublishesUpdate()
    {
        $chat = new Chat();
        $chat->setAuthor('unit')->setMessage('hi');

        $hub = $this->createMock(HubInterface::class);
        $hub->expects($this->once())
            ->method('publish')
            ->with($this->callback(function ($update) {
                return $update instanceof Update;
            }));

    $userRepo = $this->createMock(\App\Repository\UserRepository::class);
    $userRepo->method('findOneBy')->willReturn(null);

    $listener = new SendChatMercureListener($hub, $userRepo);

        $event = new ChatMessageEvent($chat);

        $listener->onChatMessage($event);
    }
}
