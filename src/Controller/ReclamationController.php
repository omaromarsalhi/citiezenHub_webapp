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
use Symfony\Component\Validator\Validator\ValidatorInterface; // Add this line to import the ValidatorInterface

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function addReclamation(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        if ($request->isMethod('POST')) {
            $reclamation = new Reeclamation();

            // Manually retrieving and setting data on the new Reclamation object
            $reclamation->setSubject($request->request->get('subject'));
            $reclamation->setDescription($request->request->get('description'));
            $reclamation->setPrivateKey($request->request->get('privateKey')); // Default to 0 or any suitable default

            // Handle file upload if applicable           // Inside your addReclamation method
            $file = $request->files->all()['reclamation']['imageFile']['file'] ?? null;
            if ($file) {
                $reclamation->setImageFile($file);
            }
           


            // Validate the entity
            $errors = $validator->validate($reclamation);

            if (count($errors) > 0) {
                // Convert errors to array or handle them as needed
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
                }

                return $this->render('reclamation/index.html.twig', [
                    'errors' => $errorsArray,
                    'reclamation' => $reclamation
                ]);
            }

            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Reclamation added successfully.');

            return $this->redirectToRoute('app_reclamation');
        }

        return $this->render('reclamation/index.html.twig');
    }
    // Handle exception if something happens during file upload
    private function handleFileUpload(UploadedFile $file): string
    {
        $uploadDirectory = $this->getParameter('reclamation_images_directory');
        $safeFilename = bin2hex(random_bytes(10)); // A simple way to generate a unique file name
        $newFilename = $safeFilename . '.' . $file->guessExtension();

        try {
            $file->move($uploadDirectory, $newFilename);
        } catch (FileException $e) {
            // Handle exception if something happens during file upload
            throw new \Exception('Failed to upload file');
        }

        return $newFilename; // Return the path or filename to be stored in the database
    }
}
