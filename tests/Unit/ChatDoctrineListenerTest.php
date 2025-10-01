<?php

namespace App\Tests\Unit;

use App\Entity\Chat;
use App\Event\ChatMessageEvent;
use App\EventListener\ChatDoctrineListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChatDoctrineListenerTest extends TestCase
{
    public function testPostPersistDispatchesEvent()
    {
        $chat = new Chat();
        $chat->setAuthor('unit_test')->setMessage('hello');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($e) use ($chat) {
                return $e instanceof ChatMessageEvent && $e->chat === $chat;
            }));

        $listener = new ChatDoctrineListener($dispatcher);

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getObject')->willReturn($chat);

        $listener->postPersist($args);
    }
}
