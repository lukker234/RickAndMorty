<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    private RickAndMortyApiService $characterService;

    public function __construct(RickAndMortyApiService $characterService)
    {
        $this->characterService = $characterService;
    }

    #[Route('/locations', name: 'locations')]
    public function locations(): Response
    {
        $locations  =$this->characterService->getAllLocations();

        return $this->render('locations/locations.html.twig', [
            'locations' => $locations,
        ]);
    }
}
