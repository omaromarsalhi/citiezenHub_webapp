<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Entity\Transport;
use App\Repository\PostRepository;
use App\Repository\RatingRepository;
use App\Repository\TransportRepository;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use App\Service\ImaggaService;

class TransportController extends AbstractController
{
    #[Route('/transport', name: 'app_transport')]
    public function listAllStationsAndTransports(StationRepository $StationRep,RatingRepository $ratingRepository): Response
    {
      $stations = $StationRep->findAll();
      $transports = $this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
      $entityManager = $this->getDoctrine()->getManager();

 
      foreach ($transports as $transport) {
        $ratings = $ratingRepository->findBy(['id_Transport' => $transport->getIdTransport()]);
        $totalRatings = count($ratings);
        $sumRatings = 0;

        foreach ($ratings as $rating) {
            $sumRatings += $rating->getRating(); // Supposons que la valeur de l'évaluation soit stockée dans une propriété "value"
        }

        $averageRating = ($totalRatings > 0) ? $sumRatings / $totalRatings : 0;

        $transport->setAverageRating($averageRating);
    }
      return $this->render('transport/transport_Admin.html.twig', [
        
          'stations' => $stations,
          'transports' => $transports,
      ]);
    }
    #[Route('/transportClient', name: 'app_transport_client')]
    public function transportClient(StationRepository $StationRep,RatingRepository $ratingRepository): Response
    {


        $transports = $this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();

        foreach ($transports as $transport) {
            $ratings = $ratingRepository->findBy(['id_Transport' => $transport->getIdTransport()]);
            $totalRatings = count($ratings);
            $sumRatings = 0;
    
            foreach ($ratings as $rating) {
                $sumRatings += $rating->getRating(); // Supposons que la valeur de l'évaluation soit stockée dans une propriété "value"
            }
    
            $averageRating = ($totalRatings > 0) ? $sumRatings / $totalRatings : 0;
    
            $transport->setAverageRating($averageRating);
        }



    
        usort($transports, function($a, $b) {
            return $b->getAverageRating() <=> $a->getAverageRating();
        });

      $stations = $StationRep->findAll();
      //$transports = $this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
      $entityManager = $this->getDoctrine()->getManager();

 
    
      return $this->render('transport/showTransport.html.twig', [
        
          'stations' => $stations,
          'transports' => $transports,
      ]);
    }

    #[Route('/transportClientFilter/{id}', name: 'app_transport_client_filterer')]
    public function transportClientFiltre(StationRepository $StationRep,$id,RatingRepository $ratingRepository): Response
    {






        $repository = $this->getDoctrine()->getManager()->getRepository(Transport::class);
        
         $query = $repository->createQueryBuilder('t')
            ->where('t.Station_depart = :stationId')
            ->setParameter('stationId', $id)
            ->getQuery();
        
        $transports = $query->getResult();



        foreach ($transports as $transport) {
            $ratings = $ratingRepository->findBy(['id_Transport' => $transport->getIdTransport()]);
            $totalRatings = count($ratings);
            $sumRatings = 0;
    
            foreach ($ratings as $rating) {
                $sumRatings += $rating->getRating(); // Supposons que la valeur de l'évaluation soit stockée dans une propriété "value"
            }
    
            $averageRating = ($totalRatings > 0) ? $sumRatings / $totalRatings : 0;
    
            $transport->setAverageRating($averageRating);
        }
        usort($transports, function($a, $b) {
            return $b->getAverageRating() <=> $a->getAverageRating();
        });

        $stations = $StationRep->findAll();
       

    
      return $this->render('transport/showTransport.html.twig', [
        
          'stations' => $stations,
          'transports' => $transports,
      ]);
    }

 
    
    #[Route('/formTransport', name: 'app_addTransport')]
    public function index( ): Response
    {
        return $this->render('transport/Admin/AddTransport.html.twig');
    }


    #[Route('/addTransport', name: 'addTransport')]
    public function addTransport(Request $request, ManagerRegistry $doc, StationRepository $stationRepository,TransportRepository $transportRepository, ValidatorInterface $validator): Response
    {
        if ($request->isXmlHttpRequest()) {
            $transport = new Transport();
            $transport->setReference($request->get('reference'));
            $transport->setPrix($request->get('prix'));
            $transport->setTypeVehicule($request->get('type_vehicule'));
            $transport->setHeure($request->get('time'));
            $transport->setStationDepart($request->get('depart'));
            $transport->setStationArrive($request->get('arrive'));
            $image = $request->files->get('image');    
            if ($image) {
                $transport->setImageFile($image);
            }
    
             // Validate the station entity using Symfony Validator
             $errors = $validator->validate($transport);
 
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
            
    
            $em = $doc->getManager();
    
            try {
                $em->persist($transport);
                $em->flush();
    
                /*
                $transports = $transportRepository->findAll();
    
                $transportsArray = array_map(function ($transport) {
                    return [
                       /* 'id' => $transport->getId(),
                        'nomstation' => $transport->getNomStation(),
                        'addressstation' => $transport->getAddressStation(),
                        'Type_Vehicule' => $transport->getTypeVehicule(),
                        'image_station' => $transport->getImageStation(),*/
                 /*   ];
                }, $transports);
    */
                $responses = [
                
                    'message' => 'Transport added successfully.'
                ];        
    
                return new JsonResponse($responses);
            }
            catch (\PDOException $e) {
                // Handle database errors
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return new JsonResponse(['error' => 'DUPLICATE_ENTRY', 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                } else {
                    return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while inserting the subscription.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                } 
           } 

         }
       else {
        return new JsonResponse(['message' => 'Station non envoyé'], Response::HTTP_OK);
      }
   }

#[Route('/updateTransport/{id}', name: 'updateTransport')]
public function updatetransport(Request $request, EntityManagerInterface $entityManager, $id,TransportRepository $transportRepository,ValidatorInterface $validator): Response
{
    if ($request->isXmlHttpRequest()) {
        $transport = $entityManager->getRepository(Transport::class)->find($id);

        if (!$transport) {
            return new JsonResponse(['error' => 'NOT_FOUND', 'message' => 'transport not found.'], Response::HTTP_NOT_FOUND);
        }

        // Retrieve the updated data from the request
            $transport->setReference($request->get('reference'));
            $transport->setPrix($request->get('prix'));
            $transport->setTypeVehicule($request->get('type_vehicule'));
            $transport->setHeure($request->get('time'));
            $transport->setStationDepart($request->get('depart'));
            $transport->setStationArrive($request->get('arrive'));
            $image = $request->files->get('image');    
            if ($image) {
                $transport->setImageFile($image);
            }

             

          

        try {

            $entityManager->flush();
        
            $responses = [
                
                'message' => 'Transport added successfully.'
            ];        

            return new JsonResponse($responses);
        } 
        catch (UniqueConstraintViolationException $e) {
   
            return new JsonResponse(['error' => 'Duplicate entry detected. Please enter a unique value.'], Response::HTTP_BAD_REQUEST);
        }    
        catch (\PDOException $e) {
            // Handle database errors
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return new JsonResponse(['error' => 'DUPLICATE_ENTRY', 'message' => 'aaa'], Response::HTTP_BAD_REQUEST);
            } else {
                return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while inserting the subscription.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            } 
       } 

        
    
    } else {
        return new JsonResponse(['message' => 'Station non envoyé'], Response::HTTP_OK);
    }
}


#[Route('/transport/{id}', name: 'app_transport_delete',  methods: ['DELETE'])]
public function deletetransport($id, transportRepository $transportRepository, Request $request,EntityManagerInterface $entityManager): Response
{
    if ($request->isXmlHttpRequest()) {
        $transport = $transportRepository->find($id);
        $entityManager->remove($transport);
        $entityManager->flush();

             
        $responses = [
                
            'message' => 'Transport Deleted successfully.'
        ];        

        return new JsonResponse($responses);

         }

    return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
}
/*
#[Route('/analyze-image', name: 'analyze_image')]
public function analyzeImage(ImaggaService $imaggaService): Response
{

}
*/ 
#[Route('/rating/add', name: 'rating_add', methods: ['POST'])]
public function add(Request $request): JsonResponse
{
    // Get rating data from the request
    $ratingValue = $request->get('rating');
    $stationId =intval($request->get('stationId')); // Ensure station ID is an integer
    $userId = intval($request->get('userId')); // Ensure user ID is an integer

    $entityManager = $this->getDoctrine()->getManager();
    $ratingRepository = $this->getDoctrine()->getRepository(Rating::class);

    // Check for existing ratings using a unique combination of user and station
    $existingRating = $ratingRepository->findOneBy([
        'id_Transport' => $stationId,
    ]);

    if ($existingRating) {
        $existingRating = $ratingRepository->findOneBy([
            'id_User' => $userId,
        ]);
        if ($existingRating) {
            $existingRating->setRating($ratingValue);
            $entityManager->persist($existingRating);  
        }else{
        
        $rating = new Rating();
        $rating->setRating($ratingValue);
        $rating->setIdTransport($stationId);
        $rating->setIdUser($userId);
        $entityManager->persist($rating);
        }
        // Update existing rating if found
    
    
    } else {
        
        // Create a new Rating entity if no duplicates found
        $rating = new Rating();
        $rating->setRating($ratingValue);
        $rating->setIdTransport($stationId);
        $rating->setIdUser($userId);
        $entityManager->persist($rating);
    }

    $entityManager->flush();

    // Return a JSON response indicating success
    return new JsonResponse(['success' => true]);
}
    /**
     * @Route("/get-station-coordinates", name="get_station_coordinates")
     */
    public function getStationCoordinates(Request $request, StationRepository $stationRepository): JsonResponse|Response
    {

        if ($request->isXmlHttpRequest()) {
            $departId = $request->get('departId');
            $arriveId = $request->get('arriveId');

            $departStation = $stationRepository->findBy(['id' => $departId]);
            $arriveStation = $stationRepository->findBy(['id' => $arriveId]);

            // Process depart station
            if ($departStation) {
                $departAddressParts = explode(",", $departStation[0]->getaddressstation()); // Assuming getAddress() method
                $response['depart'] = [
                    'latitude' => (float)$departAddressParts[0],
                    'longitude' => (float)$departAddressParts[1],
                ];
            }

            // Process arrive station
            if ($arriveStation) {
                $arriveAddressParts = explode(",", $arriveStation[0]->getaddressstation()); // Assuming getAddress() method
                $response['arrive'] = [
                    'latitude' => (float)$arriveAddressParts[0],
                    'longitude' => (float)$arriveAddressParts[1],
                ];
            }



            return new JsonResponse(['data'=>$response]);
        }
        return new Response('omar', Response::HTTP_BAD_REQUEST);
    }

}