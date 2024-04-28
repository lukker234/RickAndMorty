<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    #[Route('/episodes', name: 'episodes')]
    public function episodes(): Response
    {
        $characterService = new RickAndMortyApiService();
        $episodes = $characterService->getAllEpisodes();

        return $this->render('episodes/episodes.html.twig', [
            'episodes' => $episodes,
        ]);
    }
}