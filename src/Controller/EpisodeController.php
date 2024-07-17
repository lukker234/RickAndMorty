<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    private RickAndMortyApiService $characterService;

    public function __construct(RickAndMortyApiService $characterService)
    {
        $this->characterService = $characterService;
    }

    #[Route('/episodes', name: 'episodes')]
    public function episodes(): Response
    {
        $episodes = $this->characterService->getAllEpisodes();

        return $this->render('episodes/episodes.html.twig', [
            'episodes' => $episodes,
        ]);
    }
}
