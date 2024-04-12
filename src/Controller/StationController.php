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

class StationController extends AbstractController
{
   
    #[Route('/station', name: 'app_station')]
    public function contollAllStations(): Response
    {
     
     
        $station = $this->getDoctrine()->getManager()->getRepository(station::class)->findAll();
        return $this->render('station/stationAdmin.html.twig', [
            'controller_name' => 'StationController',
            'stationlist' => $station
        ]);
    }

  

    #[Route('/addStation', name: 'addStation')]
    public function addStation(Request $request,ManagerRegistry $doc): Response
    {
        if ($request->isXmlHttpRequest()) {
    

            $station = new Station();
            $nomStation=$request->get('nomStation');
            $adressStation=$request->get('adressStation');
            $Type=$request->get('type_vehicule');
            $Image=$request->files->get('image');

            $station->setNomStation($nomStation);
            $station->setAddressStation($adressStation);
            $station->setTypeVehicule($Type);
            $station->setImageFile($Image);

            $em = $doc->getManager();
       
            try {     
                $em->persist($station);
                $em->flush();

                
            } catch (\PDOException $e) {
                // Check if the exception indicates a duplicate entry error
                if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    // Return a custom error response indicating the duplicate entry
                    return new JsonResponse(['error' => 'DUPLICATE_ENTRY', 'message' => 'A subscription with the same name already exists. Please choose a different name.'], Response::HTTP_BAD_REQUEST);
                } else {
                    // Return a generic error response for other database errors
                    return new JsonResponse(['error' => 'DATABASE_ERROR', 'message' => 'An error occurred while inserting the subscription.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

            }
                return new JsonResponse([ 'message' => 'Station added successfully .' ], Response::HTTP_OK);

        }

        else
            return new JsonResponse(['message' => 'station non envoye'], Response::HTTP_OK);
    }
 


    #[Route('/station/{id}', name: 'app_station_delete', methods: ['DELETE'])]
    public function deleteStation($id, StationRepository $stationRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {
            $station = $stationRepository->find($id);
            $entityManager->remove($station);
            $entityManager->flush();

            return new JsonResponse('Abonnement supprimé avec succès', Response::HTTP_OK);
        }

        return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
    }
 

   /**
     * @Route("/stations", name="fetch_stations", methods={"GET"})
     */
    public function fetchStations(SerializerInterface $serializer): JsonResponse
    {
        // Fetch the updated list of stations from the database
        $stations = $this->getDoctrine()->getRepository(Station::class)->findAll();
        $jsonData = $serializer->serialize($stations, 'json', ['groups' => 'station_data']);

        return new JsonResponse($jsonData, 200, [], true);}

}
