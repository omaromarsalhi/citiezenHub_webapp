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

class TransportController extends AbstractController
{
    #[Route('/transport', name: 'app_transport')]
    public function contollAllStations(StationRepository $StationRep): Response
    {
     
     
        $transport = $this->getDoctrine()->getManager()->getRepository(transport::class)->findAll();
        $station = $StationRep->findAll();

        return $this->render('transport/Admin/transportAdmin.html.twig', [
            'stations' => $station,
            'transports'=>$transport
        ]);
    }

    #[Route('/formTransport', name: 'app_addTransport')]
    public function index( ): Response
    {
        return $this->render('transport/Admin/AddTransport.html.twig');
    }


    #[Route('/addTransport', name: 'addTransport')]
    public function addTransport(Request $request, ManagerRegistry $doc, StationRepository $stationRepository, ValidatorInterface $validator): Response
    {
        if ($request->isXmlHttpRequest()) {
            $transport = new Transport();
            $transport->setReference($request->get('reference'));
            $transport->setPrix($request->get('prix'));
            $transport->setTypeVehicule($request->get('type_vehicule'));
            $transport->setHeure($request->get('time'));
            $transport->setStationDepart($request->get('depart'));
            $transport->setStationArrive($request->get('arrive'));

            $transport->setImageFile($request->files->get('image'));
    
            // Validate the transport entity using Symfony Validator
            $errors = $validator->validate($transport);
    
            if (count($errors) > 0) {
                // If validation fails, return the validation errors
                $validationMessages = [];
                foreach ($errors as $error) {
                    // You can customize the error messages here as needed
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
    
               /* $transports = $transportRepository->findAll();
    
                $transportsArray = array_map(function ($transport) {
                    return [
                        'id' => $transport->getId(),
                        'nomstation' => $station->getNomStation(),
                        'addressstation' => $station->getAddressStation(),
                        'Type_Vehicule' => $station->getTypeVehicule(),
                        'image_station' => $station->getImageStation(),
                    ];
                }, $stations);
    
                // Prepare all the JSON responses in an array
    */            $responses = [
                    //'stations' => $stationsArray,
                    'message' => 'Station added successfully.'
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
   

}

#[Route('/updateTransport/{id}', name: 'updateTransport')]
public function updatetransport(Request $request, EntityManagerInterface $entityManager, $id,transportRepository $transportRepository): Response
{
    if ($request->isXmlHttpRequest()) {
        $transport = $entityManager->getRepository(transport::class)->find($id);

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
            $transport->setImageFile($request->files->get('image'));

        try {
            // Persist the changes to the database
            $entityManager->flush();
        
            $transports = $transportRepository->findAll();
        
            $transportsArray = array_map(function ($transport) {
                return [
                    'id' => $transport->getId(),
                    'reference' => $transport->getReference(),
                    'prix' => $transport->getPrix(),
                    'yype_Vehicule' =>  $transport->getTypeVehicule(),
                    'station_Depart' =>  $transport->getStationDepart(),
                    'station_Arrive' =>  $transport->getStationArrive(),
                    'heure' =>  $transport->getHeure(),
                    'image_Vicule' =>  $transport->getImageFile()

                ];
            }, $transports);
        
            // Prepare all the JSON responses in an array
            $responses = [
                'transports' => $transportsArray,
                'message' => 'transport updated successfully.'
            ];
        
            return new JsonResponse($responses, Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the update process
            return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while updating the transport.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    
    } else {
        return new JsonResponse(['error' => 'INVALID_REQUEST', 'message' => 'Invalid request.'], Response::HTTP_BAD_REQUEST);
    }
}


#[Route('/transport/{id}', name: 'app_transport_delete',  methods: ['DELETE'])]
public function deletetransport($id, transportRepository $transportRepository, Request $request,EntityManagerInterface $entityManager): Response
{
    if ($request->isXmlHttpRequest()) {
        $transport = $transportRepository->find($id);
        $entityManager->remove($transport);
        $entityManager->flush();

        $transports = $transportRepository->findAll();

       $transportsArray = array_map(function ($transport) {
                return [
                    'id' => $transport->getId(),
                    'reference' => $transport->getReference(),
                    'prix' => $transport->getPrix(),
                    'yype_Vehicule' =>  $transport->getTypeVehicule(),
                    'station_Depart' =>  $transport->getStationDepart(),
                    'station_Arrive' =>  $transport->getStationArrive(),
                    'heure' =>  $transport->getHeure(),
                    'image_Vicule' =>  $transport->getImageFile()

                ];
            }, $transports);
    
        return new JsonResponse(['transports' => $transportsArray]);

         }

    return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
}



}
