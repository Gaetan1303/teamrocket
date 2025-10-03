<?php
namespace App\Event;

use App\Entity\Chat;
use Symfony\Contracts\EventDispatcher\Event;

class ChatMessageEvent extends Event
{
    public const NAME = 'App\Event\ChatMessageEvent';

    private Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function getChat(): Chat
    {
        return $this->chat;
    }
}
