<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Reeclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceController extends AbstractController
{
    #[Route('/conference', name: 'app_conference')]
    public function showReclamations(EntityManagerInterface $entityManager, Request $request)
    {
        $page = max(1, $request->query->getInt('page', 1));
        $maxResults = 5; // Set the number of reclamations you want per page

        // Use QueryBuilder to create a query that left joins Reponse
        $queryBuilder = $entityManager->getRepository(Reeclamation::class)->createQueryBuilder('r')
            ->leftJoin('r.reponse', 'resp') // Ensure this matches your property name in Reeclamation
            ->addSelect('resp') // Select the joined Reponse entities as well
            ->orderBy('r.createdAt', 'DESC');

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($queryBuilder);
        $paginator->getQuery()
            ->setFirstResult($maxResults * ($page - 1)) // Offset
            ->setMaxResults($maxResults); // Limit

        $totalReclamations = count($paginator);
        $totalPages = ceil($totalReclamations / $maxResults);

        return $this->render('conference/backReclamation.html.twig', [
            'reclamations' => iterator_to_array($paginator),
            'totalPages' => $totalPages,
            'currentPage' => $page,
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



    #[Route('/reclamation/{id}/add-response', name: 'add_response_to_reclamation', methods: ['POST'])]
public function addResponseToReclamation(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    // Retrieve the specific reclamation
    $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);

    if (!$reclamation) {
        // If the reclamation is not found, return an error message
        return new JsonResponse(['error' => 'Reclamation not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Create and set the response
    $responseText = $request->request->get('response', ''); // You might want to validate this
    if (empty($responseText)) {
        // If the response text is empty, return an error message
        return new JsonResponse(['error' => 'Response text is required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $response = new Reponse();
    $response->setRepReclamation($responseText);
    $response->setReclamation($reclamation);

    // Persist the response
    $entityManager->persist($response);
    $entityManager->flush();

    // Return a success message
    return new JsonResponse(['message' => 'Response added successfully']);
}


#[Route('/reclamation/{id}/get-responses', name: 'get_responses_for_reclamation', methods: ['GET'])]
public function getResponsesForReclamation(int $id, EntityManagerInterface $entityManager): JsonResponse
{
    // Retrieve the specific reclamation
    $reclamation = $entityManager->getRepository(Reeclamation::class)->find($id);

    if (!$reclamation) {
        // If the reclamation is not found, return an error message
        return new JsonResponse(['error' => 'Reclamation not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Retrieve the responses associated with the reclamation
    $responses = $reclamation->getResponses();

    // Prepare an array to hold the response data
    $responseData = [];

    // Iterate over the responses and extract relevant information
    foreach ($responses as $response) {
        $responseData[] = [
            'id' => $response->getId(),
            'text' => $response->getRepReclamation(),
            'created_at' => $response->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }

    // Return the response data
    return new JsonResponse($responseData);
}

}