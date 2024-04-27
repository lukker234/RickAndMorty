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
            $dimensions = [];
            $locations = $this->getAllPages('https://rickandmortyapi.com/api/location');

            foreach ($locations as $location) {
                $dimension = $location['dimension'];
                $dimensionExists = false;

                // Check if the dimension already exists in the $dimensions array
                foreach ($dimensions as $existingDimension) {
                    if ($existingDimension['name'] === $dimension) {
                        // Merge residents
                        $existingDimension['residents'] = array_merge($existingDimension['residents'], array_map(function ($residentUrl) {
                            return $residentUrl;
                        }, $location['residents']));

                        $dimensionExists = true;
                        break;
                    }
                }

                // If the dimension doesn't exist, add it to the $dimensions array
                if (!$dimensionExists) {
                    $dimensions[] = [
                        'name' => $dimension,
                        'type' => $location['type'],
                        'residents' => array_map(function ($residentUrl) {
                            return $residentUrl;
                        }, $location['residents']),
                    ];
                }
            }

            return $dimensions;
        } catch (Exception $e) {
            return ['Error: ' . $e->getMessage()];
        }
    }

    public function getEpisodeDetails(): array
    {
        return $this->getAllPages('https://rickandmortyapi.com/api/episode');
    }
}