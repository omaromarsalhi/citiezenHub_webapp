<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $rec = [];
        foreach ($recc as $reclamation) {
            $subject = $reclamation->getSubject();

            if (!isset($rec[$subject]) || $rec[$subject]->getCreatedAt() < $reclamation->getCreatedAt()) {
                $rec[$subject] = $reclamation;
            }
        }

        return $this->render('reclamationsh/index.html.twig', [
            'controller_name' => 'ReclamationshController',
            'recc' => $rec
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


    
#[Route('/b/{id}', name: 'reclamationsh.del', methods: ['POST'])]
public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
{
    $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);

    if (!$reclamation) {
        return $this->json(['success' => false, 'message' => 'Reclamation not found.'], 404);
    }
    
    $entityManager->remove($reclamation);
    $entityManager->flush();
    
    return $this->json(['success' => true, 'message' => 'Reclamation deleted successfully.']);
}




#[Route('/reclamation/details/{id}', name: 'reclamation_details')]
public function getReclamationDetails(EntityManagerInterface $entityManager, $id): JsonResponse
{
    $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);

    if (!$reclamation) {
        return $this->json(['error' => 'Reclamation not found'], 404);
    }

    // Only return the image file name if `getImagePath` includes the full path.
    // Adjust as needed if `getImagePath` includes the full path.
    $imageFileName = basename($reclamation->getImagePath());

    return $this->json([
        'subject' => $reclamation->getSubject(),
        'description' => $reclamation->getDescription(),
        'createdAt' => $reclamation->getCreatedAt()->format('Y-m-d H:i:s'),
        'imagePath' => $imageFileName, // Ensure this is just the file name.
        // Add other fields as needed
    ]);
}


}
