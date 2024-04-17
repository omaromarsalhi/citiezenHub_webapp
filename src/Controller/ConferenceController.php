<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceController extends AbstractController
{
    #[Route('/conference', name: 'app_conference')]
    public function showReclamations(EntityManagerInterface $entityManager)
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
        return $this->render('conference/backReclamation.html.twig', [
            'reclamations' => $rec,
        ]);
    }
  

    // Inside ConferenceController
    
    #[Route('/get-latest-reclamations', name: 'get_latest_reclamations')]
    public function getLatestReclamations(EntityManagerInterface $entityManager): JsonResponse
    {
        // Example query to fetch the latest reclamations, adjust according to your needs
        $query = $entityManager->createQuery(
            'SELECT r FROM App\Entity\Reeclamation r ORDER BY r.createdAt DESC'
        )->setMaxResults(1); // Limit to the last 10 entries, adjust as needed
    
        $reclamations = $query->getResult();
    
        // Prepare the data for JSON response
        $data = array_map(function ($reclamation) {
            return [
                'id' => $reclamation->getId(),
                'imagePath' => $reclamation->getImagePath(),
                'privateKey' => $reclamation->getPrivateKey(),
                'createdAt' => $reclamation->getCreatedAt()->format('Y-m-d\TH:i:sP'),
                'subject' => $reclamation->getSubject(),
                'description' => $reclamation->getDescription(),
            ];
        }, $reclamations);
    
        return new JsonResponse(['reclamations' => $data]);
    }
}
