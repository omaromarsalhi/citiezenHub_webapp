<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use App\Repository\PostRepository;
use App\Repository\TransportRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransportController extends AbstractController
{
    #[Route('/transport', name: 'app_transport')]
    public function contollAllStations(): Response
    {
     
     
        $transport = $this->getDoctrine()->getManager()->getRepository(transport::class)->findAll();
        return $this->render('transport/Admin/transportAdmin.html.twig', [
            'controller_name' => 'TransportController',
            'transportlist' => $transport
        ]);
    }

    #[Route('/formTransport', name: 'app_addTransport')]
    public function index( ): Response
    {

        return $this->render('transport/Admin/AddTransport.html.twig');
    }



}
