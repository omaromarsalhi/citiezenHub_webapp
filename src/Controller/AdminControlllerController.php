<?php

namespace App\Controller;

use App\Entity\Municipalite;
use App\Repository\MunicipaliteRepository;
use App\Repository\UserRepository;
use App\Utils\ImageHelper;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminControlllerController extends AbstractController
{
    #[Route('/admin/controlller', name: 'app_admin_controlller')]
    public function index(): Response
    {
        return $this->render('admin/sign-in.html.twig');
    }

    #[Route('/Show', name: 'Show')]
    public function Show(UserRepository $rep): Response
    {
        $user = $rep->findAll();
        return $this->render('admin/customers.html.twig', [
            'list' => $user,
        ]);
    }

    #[Route('/ShowDetails/{user}', name: 'Show_Details')]
    public function ShowDetailsUser($user, UserRepository $rep): Response
    {
        $user = $rep->findOneBy(['email' => $user]);
        $now = new DateTime();
        $diffDays = $user->getDate()->diff($now)->days;
        $diffDays = sprintf("%d jours", $diffDays);
        $diffMonths = $user->getDate()->diff($now)->m;
        $diffyears = $user->getDate()->diff($now)->y;
        if ($diffyears !== 0) {
            $diffyears = sprintf("%d years", $diffyears);
            return $this->render('admin/customer-details.html.twig', [
                'user' => $user,
                'subscriptionPeriod' => $diffyears,
            ]);
        }
        if ($diffMonths !== 0) {
            $diffMonths = sprintf("%d months", $diffMonths);
            return $this->render('admin/customer-details.html.twig', [
                'user' => $user,
                'subscriptionPeriod' => $diffMonths,
            ]);

        }

        return $this->render('admin/customer-details.html.twig', [
            'user' => $user,
            'subscriptionPeriod' => $diffDays,
        ]);


    }

    #[Route('/Delete/{email}', name: 'Delete')]
    public function DeleteUser($email, UserRepository $rep, ManagerRegistry $doc): Response
    {
        $user = $rep->findOneBy(['email' => $email]);
        $em = $doc->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('Show');
    }

    #[Route('/ShowMunicipality', name: 'ShowMunicipality')]
    public function ShowMunicipality(MunicipaliteRepository $rep): Response
    {
        $municipality = $rep->findAll();
        return $this->render('admin/municipalities.html.twig', [
            'list' => $municipality,
        ]);
    }

    #[Route('/ShowDetailsMunicipality/{id}', name: 'Show_Details_Municipality')]
    public function ShowDetailsMunicipality($id, MunicipaliteRepository $rep, UserRepository $repUser): Response
    {
        $municipality = $rep->find($id);
        $user = $repUser->findBy(['municipalite' => $id]);
        return $this->render('admin/Municipality-details.html.twig', [
            'list' => $municipality,
            'user' => $user,
        ]);


    }

    #[Route('/DeleteMunicipality/{id}', name: 'DeleteMunicipality')]
    public function DeleteMunicipality($id, MunicipaliteRepository $rep, ManagerRegistry $doc): Response
    {
        $municipality = $rep->find($id);
        $em = $doc->getManager();
        $em->remove($municipality);
        $em->flush();
        return $this->redirectToRoute('ShowMunicipality');
    }

    #[Route('/AddMunicipality', name: 'AddMunicipality')]
    public function AddMunicipality(MunicipaliteRepository $rep, ValidatorInterface $validator, ManagerRegistry $doc, Request $req, ImageHelper $imageHelper): Response
    {
        $municipalite = new Municipalite();
        $errorMessages = [];
        if ($req->isXmlHttpRequest()) {
            $name = $req->get('name');
            $adresse = $req->get('adresse');
            $fichierImage = $req->files->get('imagee');
            $municipalite->setName($name);
            $municipalite->setAddress($adresse);
            $municipalite->setPhoto($imageHelper->saveImages($fichierImage));
            $errors = $validator->validate($municipalite);
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $errorMessages[$field] = $error->getMessage();
                var_dump($field);
            }
            if (count($errors) === 0) {
                $em = $doc->getManager();
                $em->persist($municipalite);
                $em->flush();
                return new JsonResponse([
                    'success' => true,
                    'user' => [
                        'name' => $municipalite->getName(),
                        'image' => $municipalite->getPhoto(),
                        'adresse' => $municipalite->getAddress(),
                    ]
                ]);
            }
            return new JsonResponse([
                'success' => false,
                'errors' => $errorMessages,
            ], 422);

        }
    }
}
