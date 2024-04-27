<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    #[Route('/characters/')]
    public function allCharacters(): Response
    {
        $characterService = new RickAndMortyApiService();
        $characters = $characterService->getAllCharacters()['results'];

        return $this->render('character/character.html.twig', [
            'characters' => $characters,
        ]);
    }

    #[Route('/character/{characterId}')]
    public function getCharacterInfo(int $characterId): Response
    {
        $characterService = new RickAndMortyApiService();
        $character = $characterService->getSingleCharacter($characterId);
        $locationUrl = $character['location']['url'];

        if (!empty($locationUrl)) {
            $locationId = substr($locationUrl, strrpos($locationUrl, '/') + 1);
            $location = $characterService->getSingleLocation($locationId);
        }

        return $this->render('character/character.html.twig', [
            'character' => $character,
            'dimension' => $location['dimension'] ?? 'unknown',
        ]);
    }
}
