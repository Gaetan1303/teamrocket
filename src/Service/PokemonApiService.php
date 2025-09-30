<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PokemonApiService
{
    private HttpClientInterface $httpClient;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function getPokemon(int|string $id): ?array
    {
        $cacheKey = 'pokemon_' . strtolower((string) $id);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);

            try {
                $response = $this->httpClient->request('GET', "https://pokebuildapi.fr/api/v1/pokemon/{$id}");
                if ($response->getStatusCode() !== 200) return null;
                return $response->toArray();
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    public function exists(int|string $id): bool
    {
        return $this->getPokemon($id) !== null;
    }

    public function getSprite(int|string $id): ?string
    {
        $data = $this->getPokemon($id);
        return $data['sprites']['front_default'] ?? null;
    }

    public function getTypes(int|string $id): array
    {
        $data = $this->getPokemon($id);
        if (!$data) return [];
        return array_map(fn($t) => $t['type']['name'], $data['types']);
    }
}
