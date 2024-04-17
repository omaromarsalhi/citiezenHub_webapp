<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(Request $request,EntityManagerInterface $manager

    ): Response
    {
        $reclamation=new Reeclamation;


        $form=$this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
           $reclamation=$form->getData();           
           $manager->persist($reclamation);
           $manager->flush();
        
           $this->addFlash('success','Votre reclamation a été envoyé avec succés');

           return $this->redirectToRoute('app_reclamation');
        }
        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
           // 'user' => $this->getUser(), 
        ]);
        
    }
   


}
