<?php

namespace services;

use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use RuntimeException;

class RickAndMortyApiService
{
    private readonly FilesystemAdapter $cache;
    private readonly Client $client;
    private readonly LoggerInterface $logger;
    private readonly string $baseUrl;

    public function __construct(Client $client, FilesystemAdapter $cache, LoggerInterface $logger, string $baseUrl)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->baseUrl = rtrim($baseUrl, '/'); // Ensure no trailing slash
    }

    private function getFromCache(string $url, callable $fetchFunction): array
    {
        $cacheKey = md5($url);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($fetchFunction) {
            $item->expiresAfter(3600);
            return $fetchFunction();
        });
    }

    public function getAllPages(string $resourceType): array
    {
        $url = $this->baseUrl . '/api/' . $resourceType;

        return $this->getFromCache($url, fn() => $this->fetchDataFromApi($url));
    }

    private function fetchDataFromApi(string $url): array
    {
        $allResults = [];
        $nextPageUrl = $url;

        do {
            $response = $this->client->request('GET', $nextPageUrl);
            $bodyContent = $response->getBody()->getContents();
            $decodedResponse = json_decode($bodyContent, true);

            if ($decodedResponse === null) {
                $this->logger->error('Error decoding JSON response from API', ['url' => $nextPageUrl]);
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

                foreach ($dimensions as $existingDimension) {
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
            $this->logger->error('Error fetching dimensions', ['exception' => $e]);
            return ['Error: ' . $e->getMessage()];
        }
    }

    public function getSingleCharacter(int $characterId): array
    {
        $url = $this->baseUrl . '/api/character/' . $characterId;
        return $this->getFromCache($url, fn() => $this->fetchDataFromApi($url));
    }

    public function getSingleLocation(int $locationId): array
    {
        $url = $this->baseUrl . '/api/location/' . $locationId;
        return $this->getFromCache($url, fn() => $this->fetchDataFromApi($url));
    }
}
