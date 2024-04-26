<?php

namespace services;

use Exception;
use GuzzleHttp\Client;

class RickAndMortyApiService
{
    public function guzzleClient(string $method, string $url): array
    {
        $client = new Client();
        $response = $client->request($method, $url);
        $bodyContent = $response->getBody()->getContents();
        $decodedResponse = json_decode($bodyContent, true);

        if ($decodedResponse === null) {
            throw new Exception('Error decoding JSON response from API');
        }

        return $decodedResponse;
    }

    public function getAllCharacters(): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/character');
    }

    public function getSingleCharacter(int $characterId): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/character/'.$characterId);
    }

    public function getAllLocations(): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/location');
    }

    public function getAllEpisodes(): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/episode');
    }

    public function getAllDimensions(): array
    {
        try {
            $data = $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/location');

            if (isset($data['results']) && is_array($data['results'])) {
                return array_map(function ($dimension) {
                    return $dimension['name'];
                }, $data['results']);
            } else {
                return ['No dimensions found.'];
            }
        } catch (Exception $e) {
            return ['Error: ' . $e->getMessage()];
        }
    }

    public function getEpisodeDetails(): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/episode');
    }
}