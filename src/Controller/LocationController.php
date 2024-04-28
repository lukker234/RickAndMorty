<?php

namespace App\Controller;

use services\RickAndMortyApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    #[Route('/locations', name: 'locations')]
    public function locations(): Response
    {
        $characterService = new RickAndMortyApiService();
        $locations = $characterService->getAllLocations();

//        dd($locations);

        return $this->render('locations/locations.html.twig', [
            'locations' => $locations,
        ]);
    }
}