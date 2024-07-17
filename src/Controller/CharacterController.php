<?php

namespace App\Controller;

use Exception;
use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    private RickAndMortyApiService $characterService;

    public function __construct(RickAndMortyApiService $characterService)
    {
        $this->characterService = $characterService;
    }

    #[Route('/characters/', name: 'characters')]
    public function allCharacters(): Response
    {
        $characters = $this->characterService->getAllCharacters()['results'];

        return $this->render('character/character.html.twig', [
            'characters' => $characters,
        ]);
    }

    #[Route('/character/{characterId}', name: 'character')]
    public function getCharacterInfo(int $characterId): Response
    {
        try {
            $character = $this->characterService->getSingleCharacter($characterId);
            $dimension = 'unknown';

            if (isset($character['location']['url'])) {
                $locationUrl = $character['location']['url'];
                $locationId = substr($locationUrl, strrpos($locationUrl, '/') + 1);
                $location = $this->characterService->getSingleLocation($locationId);
                $dimension = $location['dimension'] ?? 'unknown';
            }

            return $this->render('character/character.html.twig', [
                'character' => $character,
                'dimension' => $dimension,
            ]);
        } catch (Exception $e) {
            // Handle API exceptions or not found errors
            throw $this->createNotFoundException('Character not found');
        }
    }
}
