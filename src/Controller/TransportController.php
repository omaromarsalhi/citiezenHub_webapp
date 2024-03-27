<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransportController extends AbstractController
{
    #[Route('/addAbonnement', name: 'app_transport')]
    public function index(): Response
    {
        return $this->render('transport/createAbonnement.html.twig', [
            'controller_name' => 'TransportController',
        ]);
    }

}
