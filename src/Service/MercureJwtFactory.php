<?php
namespace App\Service;

use Firebase\JWT\JWT;

class MercureJwtFactory
{
    private string $publisherKey;
    private string $subscriberKey;

    public function __construct(string $publisherKey, string $subscriberKey)
    {
        $this->publisherKey = $publisherKey;
        $this->subscriberKey = $subscriberKey;
    }

    public function createPublisherJwt(): string
    {
        $payload = [
            'mercure' => ['publish' => ['*']],
            'exp' => time() + 3600,
        ];
        return JWT::encode($payload, $this->publisherKey, 'HS256');
    }

    public function createSubscriberJwt(): string
    {
        $payload = [
            'mercure' => ['subscribe' => ['*']], // le client pourra s'abonner à tous les topics
            'exp' => time() + 3600,
        ];
        return JWT::encode($payload, $this->subscriberKey, 'HS256');
    }
}
