<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Station;
use App\Form\StationType;
use App\Repository\StationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class StationController extends AbstractController
{
   
    #[Route('/station', name: 'app_station')]
    public function contollAllStations(): Response
    {
     
     
        $station = $this->getDoctrine()->getManager()->getRepository(station::class)->findAll();
        return $this->render('station/station_Admin.html.twig', [
            'controller_name' => 'StationController',
            'stationlist' => $station
        ]);
    }
    #[Route('/stationClient', name: 'stationClient')]
    public function showMap(): Response
    {
     
     
        $station = $this->getDoctrine()->getManager()->getRepository(station::class)->findAll();
        return $this->render('station/stations.html.twig', [
            'controller_name' => 'StationController',
            'stationlist' => $station
        ]);
    }
 

 #[Route('/addStation', name: 'addStation')]
 public function addStation(Request $request, ManagerRegistry $doc, StationRepository $stationRepository, ValidatorInterface $validator): Response
 {
     if ($request->isXmlHttpRequest()) {
         $station = new Station();
         $station->setNomStation($request->get('nomStation'));
         $station->setAddressStation($request->get('adressStation'));
         $station->setTypeVehicule($request->get('type_vehicule'));
         $station->setImageFile($request->files->get('image'));
 
         // Validate the station entity using Symfony Validator
         $errors = $validator->validate($station);
 
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
             $em->persist($station);
             $em->flush();
 
             $stations = $stationRepository->findAll();
 
             $stationsArray = array_map(function ($station) {
                 return [
                     'id' => $station->getId(),
                     'nomstation' => $station->getNomStation(),
                     'addressstation' => $station->getAddressStation(),
                     'Type_Vehicule' => $station->getTypeVehicule(),
                     'image_station' => $station->getImageStation(),
                 ];
             }, $stations);
 
             // Prepare all the JSON responses in an array
             $responses = [
                 'stations' => $stationsArray,
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


    #[Route('/updateStation/{id}', name: 'updateStation')]
    public function updateStation(Request $request, EntityManagerInterface $entityManager, $id,StationRepository $stationRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $station = $entityManager->getRepository(Station::class)->find($id);
    
            if (!$station) {
                return new JsonResponse(['error' => 'NOT_FOUND', 'message' => 'Station not found.'], Response::HTTP_NOT_FOUND);
            }
    
            // Retrieve the updated data from the request
            $nomStation = $request->get('nomStation');
            $adressStation = $request->get('adressStation');
            $type = $request->get('type_vehicule');
            $image = $request->files->get('image');
    
            // Update the station entity with the new data
            $station->setNomStation($nomStation);
            $station->setAddressStation($adressStation);
            $station->setTypeVehicule($type);
    
            if ($image) {
                $station->setImageFile($request->files->get('image'));
            }
    
            try {
                $entityManager->flush();
            
                $stations = $stationRepository->findAll();
            
                $stationsArray = array_map(function ($station) {
                    return [
                        'id' => $station->getId(),
                        'nomstation' => $station->getnomstation(),
                        'addressstation' => $station->getaddressstation(),
                        'Type_Vehicule' => $station->getTypeVehicule(),
                        'image_station' => $station->getImageStation(),
                    ];
                }, $stations);
            
                // Prepare all the JSON responses in an array
                $responses = [
                    'stations' => $stationsArray,
                    'message' => 'Station updated successfully.'
                ];
            
                return new JsonResponse($responses, Response::HTTP_OK);
            } catch (\Exception $e) {
                // Handle any exceptions that occur during the update process
                return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while updating the station.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
        
        } else {
            return new JsonResponse(['error' => 'INVALID_REQUEST', 'message' => 'Invalid request.'], Response::HTTP_BAD_REQUEST);
        }
    }
    

    #[Route('/station/{id}', name: 'app_station_delete', methods: ['DELETE'])]
    public function deleteStation($id, StationRepository $stationRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {
            $station = $stationRepository->find($id);
            $entityManager->remove($station);
            $entityManager->flush();

            $stations = $stationRepository->findAll();

            $stationsArray = array_map(function ($station) {
                return [
                    'id' => $station->getId(),
                    'nomstation' => $station->getnomstation(),
                    'addressstation' => $station->getaddressstation(),
                    'Type_Vehicule' => $station->getTypeVehicule(),
                    'image_station' => $station->getImageStation(),

                ];
            }, $stations);
        
            return new JsonResponse(['stations' => $stationsArray]);

             }

        return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
    }
 
    #[Route('/searchStation/{nameStation}', name: 'search_station', methods: ['GET'])]
    public function search(StationRepository $stationRepository ,String $nameStation): Response
    {
        $stations = $stationRepository->findBynomStation($nameStation);
         return new JsonResponse(['posts' => $stations]);

    }


    #[Route('/tri-stations', name: 'tri_stations')]
public function triStations(Request $request, StationRepository $stationRepository): Response
{
    if ($request->isXmlHttpRequest()) {
        $critereTri = $request->query->get('tri'); // Récupérer le critère de tri depuis la requête AJAX
        $ordre = $request->query->get('ordre', 'ASC'); // Récupérer l'ordre de tri (ASC ou DESC)

        // Effectuer le tri en fonction du critère et de l'ordre
        $stations = $stationRepository->findBy([], [$critereTri => $ordre]);

        // Préparer les données triées pour la réponse JSON
        $stationsArray = [];
        foreach ($stations as $station) {
            $stationsArray[] = [
                'id' => $station->getId(),
                'nomStation' => $station->getNomStation(),
                'addressStation' => $station->getAddressStation(),
                'typeVehicule' => $station->getTypeVehicule(),
                'imageStation' => $station->getImageStation(),
            ];
        }

        // Renvoyer les résultats triés au format JSON
        return new JsonResponse(['stations' => $stationsArray]);
    } else {
        // Si la requête n'est pas une requête AJAX, renvoyer une réponse d'erreur
        return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
    }
}
}
