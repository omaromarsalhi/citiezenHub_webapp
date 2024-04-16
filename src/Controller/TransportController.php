<?php

namespace App\Controller;

use App\Entity\Transport;
use App\Entity\Station;

use App\Form\TransportType;
use App\Repository\PostRepository;
use App\Repository\TransportRepository;
use App\Repository\StationRepository;

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
            'stations' => $station
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
            $transport->setReference($request->get('nomtransport'));
            $transport->setPrix($request->get('adresstransport'));
            $transport->setTypeVehicule($request->get('type_vehicule'));
            $transport->setHeure($request->get('type_vehicule'));
            $transport->setStationDepart($request->get('type_vehicule'));
            $transport->setStationArrive($request->get('type_vehicule'));

            $transport->setImageFile($request->files->get('image'));
    
            // Validate the transport entity using Symfony Validator
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
            } catch (\PDOException $e) {
                // Handle database errors
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    return new JsonResponse(['error' => 'DUPLICATE_ENTRY', 'message' => 'A subscription with the same name already exists. Please choose a different name.'], Response::HTTP_BAD_REQUEST);
                } else {
                    return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while inserting the subscription.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } else {
            return new JsonResponse(['message' => 'Station non envoyé'], Response::HTTP_OK);
        }
    }
   

}
