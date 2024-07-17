<?php

namespace services;

use Exception;
use GuzzleHttp\Client;
use RuntimeException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RickAndMortyApiService
{
    private FilesystemAdapter $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    public function getAllPages(string $resourceType): array
    {
        $url = 'https://rickandmortyapi.com/api/' . $resourceType;
        $cacheKey = md5($url);
        $cachedItem = $this->cache->getItem($cacheKey);

        if (!$cachedItem->isHit()) {
            $allResults = $this->fetchDataFromApi($url);
            $cachedItem->set($allResults);
            $cachedItem->expiresAfter(3600);
            $this->cache->save($cachedItem);
        } else {
            $allResults = $cachedItem->get();
        }

        return $allResults;
    }

    private function fetchDataFromApi(string $url): array
    {
        $client = new Client();
        $allResults = [];
        $nextPageUrl = $url;

        do {
            $response = $client->request('GET', $nextPageUrl);
            $bodyContent = $response->getBody()->getContents();
            $decodedResponse = json_decode($bodyContent, true);

            if ($decodedResponse === null) {
                throw new RuntimeException('Error decoding JSON response from API');
            }

            $allResults[] = $decodedResponse['results'] ?? $decodedResponse;
            $nextPageUrl = $decodedResponse['info']['next'] ?? null;

        } while ($nextPageUrl !== null);

        return array_merge(...$allResults);
    }

    public function getAllCharacters(): array
    {
        return $this->getAllPages('character');
    }

    public function getAllLocations(): array
    {
        return $this->getAllPages('location');
    }

    public function getAllEpisodes(): array
    {
        return $this->getAllPages('episode');
    }

    public function getAllDimensions(): array
    {
        try {
            $dimensions = [];
            $locations = $this->getAllLocations();

            foreach ($locations as $location) {
                $dimension = $location['dimension'];
                $dimensionExists = false;

                foreach ($dimensions as &$existingDimension) {
                    if ($existingDimension['name'] === $dimension) {
                        $existingDimension['residents'] = array_merge(
                            $existingDimension['residents'],
                            $location['residents']
                        );

                        $dimensionExists = true;
                        break;
                    }
                }

                if (!$dimensionExists) {
                    $dimensions[] = [
                        'name' => $dimension,
                        'type' => $location['type'],
                        'residents' => $location['residents'],
                    ];
                }
            }

            return $dimensions;
        } catch (Exception $e) {
            return ['Error: ' . $e->getMessage()];
        }
    }

    public function getSingleCharacter(int $characterId): array
    {
        return $this->getAllPages('character/' . $characterId);
    }

    public function getSingleLocation(int $locationId): array
    {
        return $this->getAllPages('location/' . $locationId);
    }
}
