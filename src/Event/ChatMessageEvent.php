<?php

namespace App\Event;

use App\Entity\Chat;

final class ChatMessageEvent
{
    public function __construct(public readonly Chat $chat)
    {
    }
}