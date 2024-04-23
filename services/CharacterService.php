<?php

namespace services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CharacterService
{
    public function guzzleClient(string $method, string $url): string
    {
        $client = new Client();
        $response = $client->request($method, $url);

        return $response->getBody()->getContents();
    }

    public function getAllCharacters(): string
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/character');
    }

    public function getAllLocations(): string
    {
        return $this->guzzleClient('GET', 'https://rickandmortyapi.com/api/location');
    }
}