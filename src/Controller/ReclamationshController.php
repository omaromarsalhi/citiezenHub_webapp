<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use App\Entity\Reponse;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ReclamationshController extends AbstractController
{
    #[Route('/b', name: 'reclamationsh')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager->getRepository(Reeclamation::class)->findBy([], ['createdAt' => 'DESC']);
        return $this->render('reclamationsh/index.html.twig', [
            'controller_name' => 'ReclamationshController',
            'recc' => $reclamations
        ]);
    }

    #[Route('/a/{id}', name: 'reclamationsh.modifer')]
    public function show(EntityManagerInterface $entityManager, $id, Request $request, ValidatorInterface $validator): Response
    {
        $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('No reclamation found with id ' . $id);
        }

        if ($request->isMethod('POST')) {
            $reclamation->setSubject($request->request->get('subject', $reclamation->getSubject()));
            $reclamation->setDescription($request->request->get('description', $reclamation->getDescription()));

            if ($file = $request->files->get('reclamation_imageFile_file')) {
                $imagePath = $this->handleFileUpload($file);
                $reclamation->setImagePath($imagePath);
            }

            $errors = $validator->validate($reclamation);
            if (count($errors) > 0) {
                return $this->render('reclamationsh/create-collection.html.twig', [
                    'controller_name' => 'ReclamationshController',
                    'reclamation' => $reclamation,
                    'errors' => $errors
                ]);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Reclamation updated successfully.');
            return $this->redirectToRoute('reclamationsh');
        }

        return $this->render('reclamationsh/create-collection.html.twig', [
            'controller_name' => 'ReclamationshController',
            'reclamation' => $reclamation
        ]);
    }

    private function handleFileUpload(UploadedFile $file): string
    {
        $uploadDirectory = $this->getParameter('reclamation_images_directory');
        $safeFilename = bin2hex(random_bytes(10));
        $newFilename = $safeFilename.'.'.$file->guessExtension();
        $file->move($uploadDirectory, $newFilename);
        return $newFilename;
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
    public function getReclamationDetails(EntityManagerInterface $entityManager, ReponseRepository $reponseRepository, $id): JsonResponse
    {
        $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);
        if (!$reclamation) {
            return $this->json(['error' => 'Reclamation not found'], 404);
        }

        $response = $reponseRepository->findByReclamationId($id);
        return $this->json([
            'subject' => $reclamation->getSubject(),
            'description' => $reclamation->getDescription(),
            'createdAt' => $reclamation->getCreatedAt()->format('Y-m-d H:i:s'),
            'imagePath' => basename($reclamation->getImagePath()),
            'response' => $response ? $response->getRepReclamation() : 'Pending'
        ]);
    }
}
