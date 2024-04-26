<?php

namespace services;

use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RickAndMortyApiService
{
    private $cache;

    public function __construct()
    {
        // Initialize the cache adapter
        $this->cache = new FilesystemAdapter();
    }

    public function getAllPages(string $url): array
    {
        $cacheKey = md5($url);
        $cachedResults = $this->cache->getItem($cacheKey);

        if (!$cachedResults->isHit()) {
            $client = new Client();
            $allResults = [];
            $nextPageUrl = $url;

            do {
                $response = $client->request('GET', $nextPageUrl);
                $bodyContent = $response->getBody()->getContents();
                $decodedResponse = json_decode($bodyContent, true);

                if ($decodedResponse === null) {
                    throw new Exception('Error decoding JSON response from API');
                }

                $allResults = array_merge($allResults, $decodedResponse['results'] ?? $decodedResponse);
                $nextPageUrl = $decodedResponse['info']['next'] ?? null;

            } while ($nextPageUrl !== null);

            $cachedResults->set($allResults);
            $this->cache->save($cachedResults);
        } else {
            $allResults = $cachedResults->get();
        }

        return $allResults;
    }

    public function getAllCharacters(): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/character');
    }

    public function getSingleCharacter(int $characterId): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/character/'.$characterId);
    }

    public function getAllLocations(): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/location');
    }

    public function getAllEpisodes(): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/episode');
    }

    public function getAllDimensions(): array
    {
        try {
            $data = $this->getAllPages('https://rickandmortyapi.com/api/location');

            if (isset($data) && is_array($data)) {
                return array_map(function ($dimension) {
                    return $dimension;
                }, $data);
            } else {
                return ['No dimensions found.'];
            }
        } catch (Exception $e) {
            return ['Error: ' . $e->getMessage()];
        }
    }

    public function getEpisodeDetails(): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/episode');
    }
}