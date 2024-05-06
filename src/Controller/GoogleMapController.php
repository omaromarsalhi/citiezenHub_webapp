<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Ivory\GoogleMap\Map;

class GoogleMapController extends AbstractController
{
    #[Route('/google', name: 'google')]
    public function showMap(): Response
    {
        $map = new Map();

        return $this->render('google/map.html.twig', [
            'map' => $map,
        ]);
    }


}