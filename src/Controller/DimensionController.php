<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DimensionController extends AbstractController
{
    private RickAndMortyApiService $characterService;

    public function __construct(RickAndMortyApiService $characterService)
    {
        $this->characterService = $characterService;
    }

    #[Route('/dimensions', name: 'dimensions')]
    public function dimensions(): Response
    {
        $dimensions = $this->characterService->getAllDimensions();

        return $this->render('dimensions/dimensions.html.twig', [
            'dimensions' => $dimensions,
        ]);
    }
}
