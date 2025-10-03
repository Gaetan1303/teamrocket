<?php
namespace App\EventListener;

use App\Entity\Chat;
use App\Event\ChatMessageEvent;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ChatDoctrineListener
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    // Doctrine will call this listener with LifecycleEventArgs; ensure we only dispatch for Chat entities
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Chat) {
            return;
        }

        $this->dispatcher->dispatch(new ChatMessageEvent($entity));
    }
}