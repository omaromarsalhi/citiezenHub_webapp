<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\Transport;
use App\Form\AbonnementType;
use App\Form\TransportType;
use App\Repository\AbonnementRepository;
use App\Repository\TransportRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ImaggaService;





class AbonnementController extends AbstractController
{


    #[Route('/showAbonnement', name: 'show_abonnement')]
    public function afficherAbn(): Response
    {
        $abonnement = $this->getDoctrine()->getManager()->getRepository(Abonnement::class)->findAll();
        return $this->render('abonnement/showAbonnement.html.twig', [
            'l' => $abonnement
        ]);
    }

    #[Route('/showAbonnement/{id}', name: 'app_abonnement_delete', methods: ['DELETE'])]
    public function deleteAbonnement($id, AbonnementRepository $abonnementRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $abonnement = $abonnementRepository->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($abonnement);
            $em->flush();

            return new JsonResponse('Abonnement supprimé avec succès', Response::HTTP_OK);
        }

        // Handle non-AJAX request, if needed

        return new JsonResponse('This route accepts only AJAX requests', Response::HTTP_BAD_REQUEST);
    }

 

    #[Route('/TransportAdmin', name: 'adminTransport')]
    public function afficherTransport(): Response
    {
        // $abonnement=$this->getDoctrine()->getManager()->getRepository(Transport::class)->findAll();
        return $this->render('abonnement/Admin/Transport.html.twig', [
            //     'l' => $abonnement
        ]);
    }


 
 
    #[Route('/formAbonnement', name: 'app_addAbonnement')]
    public function index( ): Response
    {

        return $this->render('abonnement/createAbonnement.html.twig');
    }
    #[Route('/addAbonnement', name: 'addAbonnement')]
    public function addAbonnement(Request $request,ManagerRegistry $doc): Response
    {
        if ($request->isXmlHttpRequest()) {
            $abonnement = new Abonnement();
            $Name=$request->get('name');
            $Lastname=$request->get('lastname');
            $Type=$request->get('type');
            $Image=$request->files->get('image');

            $abonnement->setNom($Name);
            $abonnement->setPrenom($Lastname);
            $abonnement->setTypeAbonnement($Type);
            $abonnement->setImageFile($Image);
            $em = $doc->getManager();

            $em->persist($abonnement);
            $em->flush();
          
         

            return new JsonResponse(['message' => $Image], Response::HTTP_OK);
        }

        else
            return new JsonResponse(['message' => 'Abonnement non envoye'], Response::HTTP_OK);
    }


    #[Route('/AbonnementAdmin', name: 'adminAbonnement')]
    public function abonnemntAdmin(): Response
    {
        $abonnement = $this->getDoctrine()->getManager()->getRepository(Abonnement::class)->findAll();
        return $this->render('abonnement/Admin/abonnementAdmin.html.twig', [
            'list' => $abonnement

        ]);
    }

    #[Route('/AbonnementScan', name: 'imageScan')]
    public function imageScan(ImaggaService $imaggaService): JsonResponse
    {
      
        // Example usage: tagging an image
        $imageUrl = 'https://images.unsplash.com/photo-1522529599102-193c0d76b5b6?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YmxhY2slMjBtYW58ZW58MHx8MHx8fDA%3D';
        $tags = $imaggaService->tagImage($imageUrl);
    
        // Return the tags as JSON response
        return new JsonResponse($tags);
    }
}