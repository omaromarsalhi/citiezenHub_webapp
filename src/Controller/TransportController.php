<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Entity\Station;

use App\Form\TransportType;
use App\Repository\PostRepository;
use App\Repository\TransportRepository;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class TransportController extends AbstractController
{
    #[Route('/transport', name: 'app_transport')]
    public function listAllStationsAndTransports(StationRepository $StationRep): Response
    {
      $stations = $StationRep->findAll();
      $transports = $this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
      $entityManager = $this->getDoctrine()->getManager();

 
    
      return $this->render('transport/Admin/transportAdmin.html.twig', [
        
          'stations' => $stations,
          'transports' => $transports,
      ]);
    }
    #[Route('/transportClient', name: 'app_transport_client')]
    public function transportClient(StationRepository $StationRep): Response
    {
      $stations = $StationRep->findAll();
      $transports = $this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
      $entityManager = $this->getDoctrine()->getManager();

 
    
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



}
