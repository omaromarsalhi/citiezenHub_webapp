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


    #[Route('/showAbonnement', name: 'app_transport')]
    public function afficherAbn(): Response
    {
        $transport=$this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
        return $this->render('transport/showAbonnement.html.twig', [
            'l' => $transport
        ]);
    }

    #[Route('/showAbonnement/{id}', name: 'app_transport_delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, $id, TransportRepository $transportRepository, Request $req): Response
    {
        if ($req->isXmlHttpRequest()) {
            $auteur = $transportRepository->find($id);
            $em = $doctrine->getManager();
            $em->remove($auteur);
            $em->flush();
            return new Response('Transport supprimé avec succès', Response::HTTP_OK);
        }
        return $this->redirectToRoute('app_transport');
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
