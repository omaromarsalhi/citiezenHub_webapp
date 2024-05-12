<?php

namespace App\Controller;


use App\Entity\Municipalite;
use App\MyHelpers\AiVerification;
use App\MyHelpers\ImageHelperUser;
use App\Repository\MunicipaliteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ivory\GoogleMap\Map;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user', methods: ['GET', 'POST'])]
    public function index(): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/cinUpdate', name: 'app_user_cinUpdate', methods: ['GET', 'POST'])]
    public function cinUpdate(AiVerification $aiVerification, Request $request, EntityManagerInterface $entityManager, ImageHelperUser $imageHelperUser): Response
    {
        if ($request->isXmlHttpRequest()) {

            $frontId = $request->files->get('frontId');
            $backId = $request->files->get('backId');
            $frontIdPath = $imageHelperUser->saveImages($frontId);
            $backIdPath = $imageHelperUser->saveImages($backId);
            $user = $this->getUser();
            $user->setCinImages($frontIdPath . '_' . $backIdPath);
            $entityManager->flush();

            $obj = [
                'pathFrontCin' => '../citiezenHub_webapp/public/usersImg/' . $frontIdPath,
                'pathBackCin' => '../citiezenHub_webapp/public/usersImg/' . $backIdPath,
                'fileNameFront' => md5('user_front' . ($user->getId() * 1000 + 17)),
                'fileNameBackCin' => md5('user_backCin' . ($user->getId() * 1000 + 17)),
            ];

            $aiVerification->runOcr($obj);

            return new Response('done', Response::HTTP_OK);
        }

        return new Response('error', Response::HTTP_FORBIDDEN);

    }


    #[Route('/updateAddress', name: 'app_user_updateAddress', methods: ['GET', 'POST'])]
    public function updateAddress(Request $request, EntityManagerInterface $entityManager, MunicipaliteRepository $municipalityRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $municipalityName = $request->get('municipality');
            $mapAddress = $request->get('mapAddress');
            $municipalityAddress = $request->get('municipalityAddressNew');
            $state = $request->get('state');

            var_dump($municipalityAddress);
            if (!$this->getUser()) {
                return new Response('error', Response::HTTP_FORBIDDEN);
            }

            $user = $this->getUser();
            $user->setAddress($mapAddress);

            if ($user->getMunicipalite() !== null && $user->getMunicipalite()->getName() === $municipalityName) {
                $entityManager->flush();
                return new Response('done', Response::HTTP_OK);
            }

            $municipality = new Municipalite();
            $municipality->setName($municipalityName);
            $municipality->setAddress($municipalityAddress);
            $municipality->setGoverment($state);

            $user->setMunicipalite($municipality);

            $entityManager->persist($municipality);
            $entityManager->flush();


            return new Response('done', Response::HTTP_OK);
        }
        return new Response('error', Response::HTTP_FORBIDDEN);
    }


    #[Route('/editProfile', name: 'editProfile', methods: ['GET', 'POST'])]
    public function editUser(UserRepository $rep, ManagerRegistry $doc, Request $req, ValidatorInterface $validator, ImageHelperUser $imageHelper, SessionInterface $session): Response
    {
        $user = $rep->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $routePrecedente = $req->headers->get('referer');
        $parsedUrl = parse_url($routePrecedente);
        $path = $parsedUrl['path'];
        $alertMessage = "Votre profil a été modifié avec succès !";
        $session->set('profile_alert_message', $alertMessage);
        $currentDate = $user->getDate();
        $expiryTime = $currentDate->modify('+5 minutes');
        $session->set('profile_alert_expiry', $expiryTime);
        $errorMessages = [];
        $current = new \DateTime('now', new \DateTimeZone('Africa/Tunis'));
        if ($req->isXmlHttpRequest()) {
            if ($current->getTimestamp() < $expiryTime->getTimestamp() || $user->getState()) {
//                $emailService->envoyerEmail($mailer);
                $email = $req->get('email');
                $name = $req->get('name');
                $lastname = $req->get('lastname');
                $age = $req->get('age');
                $gender = $req->get('gender');
                $status = $req->get('status');
                $cin = $req->get('cin');
                $phoneNumber = $req->get('phoneNumber');
                $date = $req->get('date');
                $fichierImage = $req->files->get('image');
                $user->setFirstName($name);
                $user->setLastName($lastname);
                $user->setAge($age);
                $user->setPhoneNumber($phoneNumber);
                $user->setCin($cin);
                $user->setStatus($status);
                $user->setGender($gender);
                if ($fichierImage != null)
                    $user->setImage($imageHelper->saveImages($fichierImage));
                $datee = date_create($date);
                $user->setDob($datee);
                $errors = $validator->validate($user, null, 'creation');
                foreach ($errors as $error) {
                    $field = $error->getPropertyPath();
                    $errorMessages[$field] = $error->getMessage();
                }
                if (count($errors) === 0) {
                    $em = $doc->getManager();
                    $em->persist($user);
                    $em->flush();

                    return new JsonResponse([
                        'success' => true,
                        'user' => [
                            'name' => $user->getFirstName(),
                            'lastname' => $user->getLastName(),
                            'email' => $user->getEmail(),
                            'address' => $user->getAddress(),
                            'cin' => $user->getCin(),
                            'phoneNumber' => $user->getPhoneNumber(),
                            'age' => $user->getAge(),
                            'status' => $user->getStatus(),
                            'image' => $user->getImage(),
                            'gender' => $user->getGender(),
                            'dob' => $user->getDob(),
                        ]
                    ]);
                }
                return new JsonResponse([
                    'success' => false,
                    'errors' => $errorMessages,
                ], 422);

            } else
                return new JsonResponse([
                    'redirect' => $this->generateUrl('page404')
                ]);
        }

        $map = new Map();
        if ($user->getCinImages() === null) {
            $cinImages=['none','none'];
        } else
            $cinImages = explode("_", $user->getCinImages());


        return $this->render('user/edit_profile.html.twig', [
            'name' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'cin' => $user->getCin(),
            'phoneNumber' => $user->getPhoneNumber(),
            'age' => $user->getAge(),
            'status' => $user->getStatus(),
            'image' => $user->getImage(),
            'gender' => $user->getGender(),
            'dob' => $user->getDob(),
            'errors' => $errorMessages,
            'routePrecedente' => $path,
            'expiry_time' => $expiryTime,
            'date' => $user->getDate(),
            'cinFront' => $cinImages[0] ,
            'cinBack' => $cinImages[1] ,
            'map' => $map,
        ]);
    }


    #[Route('/editImage', name: 'editImage', methods: ['GET', 'POST'])]
    public function editUserImage(EntityManagerInterface $entityManager, Request $req, ImageHelperUser $imageHelper): Response
    {
        if ($req->isXmlHttpRequest()) {
            $fichierImage = $req->files->get('imagee');
            $user = $this->getUser();
            if ($fichierImage != null)
                $user->setImage($imageHelper->saveImages($fichierImage));
            $entityManager->flush();
            return new Response(' supprimé avec succès false ', Response::HTTP_OK);
        }
        return new Response('bad');


    }

    #[Route('/delete', name: 'app_user_delete')]
    public function delete(ManagerRegistry $doctrine, UserRepository $userRepository, Request $req): Response
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $em = $doctrine->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_login');
//        return new Response(' supprimé avec succès false ', Response::HTTP_OK);

    }


    #[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function consulterProfile(Request $req): Response
    {
        $name = $req->get('name');
        dump($name);
        $lastname = $req->get('lastname');
        dump($lastname);
        $email = $req->get('email');
        dump($email);
        $image = $req->get('image');
        dump($image);
        return $this->render('user/profile.html.twig', [
            'name' => $name,
            'lastname' => $lastname,
            'email' => $email,
            'image' => $image,
        ]);
    }

    #[Route('/changePassword', name: 'changePassword', methods: ['GET', 'POST'])]
    public function changePassword(UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doc, UserRepository $userRepository, Request $req, ValidatorInterface $validator)
    {
        if ($req->isXmlHttpRequest()) {
            $errorMessages = [];
            $Messages = '';
            $user = $this->getUser();

            $newPassword = $req->get('NewPass');
            $confirmPassword = $req->get('rePass');
            $oldPassword = $req->get('oldPass');
            if ($userPasswordHasher->isPasswordValid($user, $oldPassword) && strcmp($newPassword, $confirmPassword) == 0) {
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($newPassword);
                $errors = $validator->validate($user, null, 'editPassword');
                foreach ($errors as $error) {
                    $field = $error->getPropertyPath();
                    $errorMessages[$field] = $error->getMessage();
                    dump($field);
                }
                $errorMessages["other"] = 'nnnnnnnnnn';
            }

            $user->setPassword($hashedPassword);
            $em = $doc->getManager();
            $em->flush();

            return new JsonResponse([
                'success' => false,
                'errors' => count($errors),
                'errorMessages' => $errorMessages,
            ], 200);
        }
        return new Response('bad');

    }


    #[Route('/page404', name: 'page404', methods: ['GET', 'POST'])]
    public function loadPage404(): Response
    {

        return $this->render('user/404.html.twig', [

        ]);
    }


}

