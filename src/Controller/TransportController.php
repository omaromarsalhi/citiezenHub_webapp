<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Form\TransportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransportController extends AbstractController
{


    #[Route('/showAbonnement', name: 'app_transport')]
    public function afficherAbn(): Response
    {
        $transport=$this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
        return $this->render('transport/showAbonnement.html.twig', [
            'l' => $transport
        ]);
    }
    #[Route('/TransportAdmin', name: 'adminTransport')]
    public function afficherTransport(): Response
    {
       // $transport=$this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
        return $this->render('transport/Admin/Transport.html.twig', [
       //     'l' => $transport
        ]);
    }

    #[Route('addTransport', name: 'addTransport')]

    public function addTransport(Request $request): Response
    {
        $transport = new Transport();
        $form = $this->createForm(TransportType::class, $transport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($transport);
            $em->flush();
            return $this->redirectToRoute('app_transport');
        }
        else{
            return $this->render('transport/createAbonnement.html.twig',['f' => $form->createView()]);
        }


    }

}
