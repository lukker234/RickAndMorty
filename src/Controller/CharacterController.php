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
        $location = $characterService->getSingleLocation(
            substr($character['location']['url'], strrpos($character['location']['url'], '/') + 1)
        );

        return $this->render('character/character.html.twig', [
            'character' => $character,
            'dimension' => $location['dimension'],
        ]);
    }
}
