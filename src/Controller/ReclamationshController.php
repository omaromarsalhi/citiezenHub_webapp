<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationshController extends AbstractController
{
    #[Route('/b', name: 'reclamationsh')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recc = $entityManager->getRepository(Reeclamation::class)->findBy([], ['createdAt' => 'DESC']);
        $last_reclamations = [];

        foreach ($recc as $reclamation) {
            $subject = $reclamation->getSubject();

            if (!isset($rec[$subject]) || $rec[$subject]->getCreatedAt() < $reclamation->getCreatedAt()) {
                $rec[$subject] = $reclamation;
            }
        }

        return $this->render('reclamationsh/index.html.twig', [
            'controller_name' => 'ReclamationshController',
            'rec' => $rec
        ]);
    }




    #[Route('/a/{id}', name: 'reclamationsh.modifer')]
    public function show(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);  
    
        if (!$reclamation) {
            throw $this->createNotFoundException('No reclamation found with id ' . $id);
        }
    

        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
           $reclamation=$form->getData();           
           $entityManager->persist($reclamation);
           $entityManager->flush();
        
           $this->addFlash('success','Votre reclamation a été envoyé avec succés');

           return $this->redirectToRoute('app_reclamation');
        }

        return $this->render('reclamationsh/create-collection.html.twig', [
            'controller_name' => 'ReclamationshController',
             'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    
        
    }
    

}
