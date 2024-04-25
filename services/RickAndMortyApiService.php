<?php

namespace services;

use GuzzleHttp\Client;

class RickAndMortyApiService
{
    public function guzzleClient(string $method, string $url): array
    {
        $client = new Client();
        $response = $client->request($method, $url);

        // Get the body content as a string
        $bodyContent = $response->getBody()->getContents();

        // Decode the JSON string into an associative array
        $decodedResponse = json_decode($bodyContent, true);

        if ($decodedResponse === null) {
            throw new \Exception('Error decoding JSON response from API');
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

    public function getEpisodeDetails(): array
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/episode');
    }
}