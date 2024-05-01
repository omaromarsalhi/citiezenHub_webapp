<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Reeclamation;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;

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
    public function addResponseToReclamation(int $id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer): JsonResponse
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

        // Validate the response entity using Symfony Validator
        $errors = $validator->validate($response);

        if (count($errors) > 0) {
            // If validation fails, return the validation errors
            $validationMessages = [];
            foreach ($errors as $error) {
                $validationMessages[] = $error->getMessage();
            }
            $responses = [
                'messages' => $validationMessages,
                'error' => 'VALIDATION_ERROR'
            ];

            return new JsonResponse($responses, Response::HTTP_BAD_REQUEST);
        }

        // Persist the response
        $entityManager->persist($response);
        $entityManager->flush();

        // Send an email notification to the user
        $email = (new Email())
            ->from('khalil.rmila@esprit.tn')
            ->to('gamerkhalil007@gmail.com') // Assuming the user's email is stored in the User entity associated with the reclamation
            ->subject('Nouvelle réponse à votre réclamation')
            ->html("
        <div style='background-image: url(\"C:/Users/khali/Downloads/logo.png\"); background-size: cover; font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <img src='path/to/logo.png' alt='Logo' style='display: block; margin: 0 auto; max-width: 200px;'>
            <div style='background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 10px;'>
                <p style='font-size: 16px; color: #333; line-height: 1.5;'>Bonjour,</p>
                <p style='font-size: 16px; color: #333; line-height: 1.5;'>Une nouvelle réponse a été ajoutée à votre réclamation :</p>
                <ul style='font-size: 16px; color: #333; line-height: 1.5; padding-left: 20px;'>
                    <li><strong>Numéro de réclamation :</strong> {$reclamation->getPrivateKey()}</li>
                    <li><strong>Date de réclamation :</strong> {$reclamation->getCreatedAt()->format('Y-m-d H:i:s')}</li>
                    <li><strong>Détails de la réclamation :</strong> {$reclamation->getDescription()}</li>
                    <li><strong>Réponse :</strong> $responseText</li>
                </ul>
                <p style='font-size: 16px; color: #333; line-height: 1.5; margin-top: 20px;'>
                    <a href='http://127.0.0.1:8000/b' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;'>Cliquez ici pour aller à la page de votre reclamtion</a>
                </p>
                <p style='font-size: 16px; color: #333; line-height: 1.5; margin-top: 20px;'>Par l'administrateur Khalil Rmila</p>
            </div>
        </div>
    ");

        $mailer->send($email);




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
    
    #[Route('/statistics', name: 'app_statistics')]
    public function showStatistics(EntityManagerInterface $entityManager): Response
    {
        // Fetch total number of reclamations
        $totalReclamations = $entityManager->getRepository(Reeclamation::class)->count([]);
    
        // Fetch number of responded reclamations using an explicit join and DQL
        $respondedReclamations = $entityManager->getRepository(Reeclamation::class)
            ->createQueryBuilder('r')
            ->leftJoin('r.reponse', 'resp')
            ->select('count(r.id)')
            ->where('resp.id IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    
        // Calculate pending reclamations by subtracting responded from total
        $pendingReclamations = $totalReclamations - $respondedReclamations;
    
        // Render the statistics view with all the data
        return $this->render('conference/statistics.html.twig', [
            'statistics' => [
                'total_reclamations' => $totalReclamations,
                'responded_reclamations' => $respondedReclamations,
                'pending_reclamations' => $pendingReclamations
            ],
        ]);
    }
    
    
    
    
    

}
