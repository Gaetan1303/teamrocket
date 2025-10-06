<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Génère des JWT valides pour publier et s'abonner au Hub Mercure
 */
class MercureJwtFactory
{
    private string $publisherKey;
    private string $subscriberKey;

    public function __construct(string $publisherKey, string $subscriberKey = null)
    {
        $this->publisherKey = $publisherKey;
        $this->subscriberKey = $subscriberKey ?? $publisherKey;
    }

    /**
     * Génère un JWT pour publier des événements
     *
     * @param array $topics Liste des topics autorisés (["*"] = tous)
     */
    public function createPublisherJwt(array $topics = ['*']): string
    {
        $payload = [
            'mercure' => [
                'publish' => $topics,
            ],
            'exp' => time() + 3600, // expire dans 1h
        ];

        return JWT::encode($payload, $this->publisherKey, 'HS256');
    }

    /**
     * Génère un JWT pour s'abonner à des événements
     *
     * @param array $topics Liste des topics autorisés (["*"] = tous)
     */
    public function createSubscriberJwt(array $topics = ['*']): string
    {
        $payload = [
            'mercure' => [
                'subscribe' => $topics,
            ],
            'exp' => time() + 3600,
        ];

        return JWT::encode($payload, $this->subscriberKey, 'HS256');
    }
}
