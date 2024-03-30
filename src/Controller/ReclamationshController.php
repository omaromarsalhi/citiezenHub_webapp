<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationshController extends AbstractController
{
    #[Route('/reclamationsh', name: 'app_reclamationsh')]
    public function index(): Response
    {
        return $this->render('reclamationsh/index.html.twig', [
            'controller_name' => 'ReclamationshController',
        ]);
    }
}
