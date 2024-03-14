<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OmarController extends AbstractController
{
    #[Route('/omar', name: 'app_omar')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'OmarController',
        ]);
    }
}
