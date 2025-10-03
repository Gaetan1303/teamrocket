<?php

namespace App\MessageHandler;

use App\Message\PurgeOldChatMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PurgeOldChatMessageHandler
{
    public function __invoke(PurgeOldChatMessage $message): void
    {
        // do something with your message
    }
}
