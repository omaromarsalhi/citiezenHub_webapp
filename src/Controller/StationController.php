<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationController extends AbstractController
{
   
    #[Route('/station', name: 'app_station')]
    public function contollAllStations(): Response
    {
        return $this->render('station/stationAdmin.html.twig', [
            'controller_name' => 'StationController',
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
            $station->setImageStation($Image);

            $em = $doc->getManager();
            $em->persist($station);
            $em->flush();

            return new JsonResponse(['message' => $Image], Response::HTTP_OK);
        }

        else
            return new JsonResponse(['message' => 'station non envoye'], Response::HTTP_OK);
    }
}
