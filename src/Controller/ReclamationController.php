<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface; // Add this line to import the ValidatorInterface

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(Request $request, EntityManagerInterface $manager, ValidatorInterface $validator): Response // Inject ValidatorInterface here
    {
        $reclamation = new Reeclamation();

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $reclamation = $form->getData();
            
            // Validate the entity
            $errors = $validator->validate($reclamation);
            
            if (count($errors) > 0) {
                // Handle validation errors, e.g., by displaying them back in the form
                // This is a simple way to convert errors to a string, customize it as needed
                $errorsString = (string) $errors;
                
                // Add flash message to display errors
                $this->addFlash('error', 'Validation failed: ' . $errorsString);
                
                // Optionally, return to the form page again if there are errors
                return $this->render('reclamation/index.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $manager->persist($reclamation);
            $manager->flush();

            $this->addFlash('success', 'Votre réclamation a été envoyée avec succès');

            return $this->redirectToRoute('app_reclamation');
        }
        return $this->render('reclamation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

