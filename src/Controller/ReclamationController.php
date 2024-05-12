<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\MyHelpers\ImageHelper;
use App\MyHelpers\ImageHelperUser;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation')]
    public function index(): Response
    {
        $privateKey = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(1, 10))), 0, 9);
        return $this->render('reclamation/contact.html.twig', [
            'controller_name' => 'ReclamationController',
            'privateKey' => $privateKey,

        ]);
    }


    #[Route('/reclamation/show', name: 'app_reclamation_show')]
    public function show(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamations' => $reclamationRepository->findBy(['user' => $this->getUser()]),
        ]);
    }



    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function new(ImageHelperUser $imageHelperUser,Request $request,EntityManagerInterface $entityManager,ValidatorInterface $validator): Response
    {
        if($request->isXmlHttpRequest()) {

            $subject=$request->request->get('subject');
            $message=$request->request->get('message');
            $privatekey=$request->request->get('privatekey');
            $fichierImage = $request->files->get('image');
            $reclamation = new Reclamation();

            $reclamation->setSubject($subject);
            $reclamation->setUser($this->getUser());
            $reclamation->setDescription($message);
            $reclamation->setPrivateKey($privatekey);

            $reclamation->setImage($imageHelperUser->saveImages($fichierImage));


            $errors = $validator->validate($reclamation);
 
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


            }
            $entityManager->persist($reclamation);
            $entityManager->flush();


            return new Response("done", Response::HTTP_OK);
        }


        return $this->render('reclamation/contact.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

    #[Route('/delete/{id}', name: 'reclamationsh')]
    public function delete(EntityManagerInterface $entityManager, $id,ReclamationRepository $reclamationRepository): Response
    {

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        $entityManager->remove($reclamation);
        $entityManager->flush();

        return $this->render('reclamation/show.html.twig', [
            'reclamations' => $reclamationRepository->findBy(['user' => $this->getUser()]),
        ]);
    }
}
