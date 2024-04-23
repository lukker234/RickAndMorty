<?php

namespace App\Controller;

use services\CharacterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        $characterService = new CharacterService();
        $episodes = $characterService->getAllEpisodes()['results'];

        return $this->render('base.html.twig', [
            'episodes' => $episodes,
        ]);
    }

    #[Route('/characters/')]
    public function allCharacters(): Response
    {
        $characterService = new CharacterService();

        return $this->render('characters.html.twig', [
            'list' => $characterService->getAllCharacters(),
        ]);
    }
}
