<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DimensionController extends AbstractController
{
    #[Route('/dimensions')]
    public function dimensions(): Response
    {
        $characterService = new RickAndMortyApiService();
        $dimensions = $characterService->getAllDimensions();

        return $this->render('dimensions/dimensions.html.twig', [
            'dimensions' => $dimensions,
        ]);
    }
}