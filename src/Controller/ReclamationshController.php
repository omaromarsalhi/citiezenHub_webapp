<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
        // Dumping request data for debugging
        
    
        $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);
    
        if (!$reclamation) {
            throw $this->createNotFoundException('No reclamation found with id ' . $id);
        }
    
        if ($request->isMethod('POST')) {
            // Since we've adjusted the form, let's access the values directly
            $subject = $request->request->get('subject');
            $description = $request->request->get('description');
    
            // Handle the file upload field directly
            $file = $request->files->get('reclamation_imageFile_file');
    
            if ($subject !== null) {
                $reclamation->setSubject($subject);
            }
    
            if ($description !== null) {
                $reclamation->setDescription($description);
            }
    
            if ($file) {
                $imagePath = $this->handleFileUpload($file);
                $reclamation->setImagePath($imagePath);
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Reclamation updated successfully.');
    
            return $this->redirectToRoute('reclamationsh');
        }
        
    
        return $this->render('reclamationsh/create-collection.html.twig', [
            'controller_name' => 'ReclamationshController',
            'reclamation' => $reclamation,
        ]);
    }
    
    private function handleFileUpload(UploadedFile $file): string
{
    $uploadDirectory = $this->getParameter('reclamation_images_directory');
    $safeFilename = bin2hex(random_bytes(10)); // A simple way to generate a unique file name
    $newFilename = $safeFilename.'.'.$file->guessExtension();

    try {
        $file->move($uploadDirectory, $newFilename);
    } catch (FileException $e) {
        // Handle exception if something happens during file upload
        throw new \Exception('Failed to upload file');
    }

    return $newFilename; // Return the path or filename to be stored in the database
}


    
    #[Route('/b/{id}', name: 'reclamationsh.del')]
        public function delete(EntityManagerInterface $entityManager, $id): Response
        {
            $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);
    
            if (!$reclamation) {
                throw $this->createNotFoundException('No reclamation found with id ' . $id);
            }
            
            $entityManager->remove($reclamation);
            $entityManager->flush();
            
            $this->addFlash('success', 'Reclamation supprimée avec succès');
            
            return $this->render('reclamationsh/index.html.twig', [
                'controller_name' => 'ReclamationshController',
                
            ]); // return an empty response with a 200 status code // replace 'reclamationsh_index' with the name of the route you want to redirect to
        }

}
