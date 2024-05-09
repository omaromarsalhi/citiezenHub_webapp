<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        return $this->render('reclamation/contact.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }


    #[Route('/reclamation/show', name: 'app_reclamation_show')]
    public function show(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamations' => $reclamationRepository->findBy(['user' => $this->getUser()]),
        ]);
    }



    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function new(Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()) {

            $subject=$request->request->get('subject');
            $message=$request->request->get('message');

            $reclamation = new Reclamation();

            $reclamation->setSubject($subject);
            $reclamation->setUser($this->getUser());
            $reclamation->setDescription($message);
            $reclamation->setPrivateKey('oooooohhh');

            $entityManager->persist($reclamation);
            $entityManager->flush();


            return new Response("done", Response::HTTP_OK);
        }


        return $this->render('reclamation/contact.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }
}
