<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\MunicipaliteRepository;
use App\Security\UserAuthanticatorAuthenticator;
use App\Utils\GeocodingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository): Response
    {
        $user = $session->get('authenticatedUser');
        $userData = $request->request->get('user');
        $errors = [];
        $errorMessages = [];
        if ($request->isMethod('POST') && $userData !== null) {
            if ($request->request->has('submit_button')) {
                $user = new User();
                $user->setFirstName($userData['firstName']);
                $user->setLastName($userData['lastName']);
                $user->setAddress($userData['address']);
                $user->setEmail($userData['email']);
                $user->setPassword($userData['password']);

                $errors = $validator->validate($user, null, 'add');

                foreach ($errors as $error) {
                    $field = $error->getPropertyPath();
                    $errorMessages[$field] = $error->getMessage();
                }
                $muni = $municipaliteRepository->findOneBy(['name' => $geo->getMunicipalityFromAddress($userData['address'])]);
                if (count($errors) === 0 && $userData['password'] === $userData['password1'] && $muni) {
                    if ($muni !== null) {
                        $hashedPassword = $userPasswordHasher->hashPassword(
                            $user,
                            $userData['password']
                        );
                        $user->setMunicipalite($muni);
                        $user->setPassword($hashedPassword);
                        $user->setRole("Citoyen");
                        $user->setDate(new DateTime());
                        $entityManager->persist($user);
                        $entityManager->flush();
                        return $userAuthenticator->authenticateUser(
                            $user,
                            $authenticator,
                            $request
                        );
                    }
                }

            }
        }
//            else {
//            return  $this->redirectToRoute('app_register');
//        }

        return $this->render('test/signup.html.twig', [
            'name' => $user ? $user->getFirstName() : '',
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $user ? $user->getEmail() : '',
            'role' => $user ? $user->getRole() : '',
            'errors' => $errorMessages ,
        ]);
    }
//
//    #[Route('/register', name: 'app_register')]
//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository): Response
//    {
//        $user = $session->get('authenticatedUser');
//        $userData = $request->request->get('user');
//        $errors = [];
//        $errorMessages = [];
//        if ($request->isXmlHttpRequest()) {
//            dump("heloooo");
//            $name = $request->get('name');
//            $lastname = $request->get('lastname');
//            $email = $request->get('email');
//            $address = $request->get('address');
//            $newpassword = $request->get('newpassword');
//            $repassword = $request->get('repassword');
//            $user = new User();
//                $user->setFirstName($name);
//                $user->setLastName($lastname);
//                $user->setAddress($address);
//                $user->setEmail($email);
//                $user->setPassword($newpassword);
//                $errors = $validator->validate($user, null, 'add');
//                foreach ($errors as $error) {
//                    $field = $error->getPropertyPath();
//                    $errorMessages[$field] = $error->getMessage();
//                }
//                $muni = $municipaliteRepository->findOneBy(['name' => $geo->getMunicipalityFromAddress($address)]);
//                if (count($errors) === 0 && $newpassword=== $repassword && $muni) {
//                    if ($muni !== null) {
//                        $hashedPassword = $userPasswordHasher->hashPassword(
//                            $user,
//                            $userData['password']
//                        );
//                        $user->setMunicipalite($muni);
//                        $user->setPassword($hashedPassword);
//                        $user->setRole("Citoyen");
//                        $user->setDate(new DateTime());
//                        $entityManager->persist($user);
//                        $entityManager->flush();
//                        return $userAuthenticator->authenticateUser(
//                            $user,
//                            $authenticator,
//                            $request
//                        );
//                    }
//                }
//
//
//        }
//
////            else {
////            return  $this->redirectToRoute('app_register');
////        }
//
//        return $this->render('test/signup.html.twig', [
//            'name' => $user ? $user->getFirstName() : '',
//            'lastName' => $user ? $user->getLastName() : '',
//            'email' => $user ? $user->getEmail() : '',
//            'role' => $user ? $user->getRole() : '',
//            'errors' => $errorMessages ,
//        ]);
//    }
//



//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository): Response
//    {
//        $user = $session->get('authenticatedUser');
//        $userData = $request->request->get('user');
//        $errors = [];
//        $errorMessages = [];
//
//        if ($request->isMethod('POST') && $userData !== null) {
//            if ($request->request->has('submit_button')) {
//                $user = new User();
//                $user->setFirstName($userData['firstName']);
//                $user->setLastName($userData['lastName']);
//                $user->setAddress($userData['address']);
//                $user->setEmail($userData['email']);
//                $user->setPassword($userData['password']);
//
//                $errors = $validator->validate($user, null, 'add');
//
//                foreach ($errors as $error) {
//                    $field = $error->getPropertyPath();
//                    $errorMessages[$field] = $error->getMessage();
//                }
//                $muni=$municipaliteRepository->findOneBy([ 'name' =>$geo->getMunicipalityFromAddress($userData['address'])]);
//                if (count($errors) === 0 && $userData['password'] === $userData['password1'] && $muni) {
//                    if ($muni !== null) {
//                        $hashedPassword = $userPasswordHasher->hashPassword(
//                            $user,
//                            $userData['password']
//                        );
//                        $user->setMunicipalite($muni);
//                        $user->setPassword($hashedPassword);
//                        $user->setRole("Citoyen");
//                        $user->setDate(new DateTime());
//                        $entityManager->persist($user);
//                        $entityManager->flush();
//                        return $userAuthenticator->authenticateUser(
//                            $user,
//                            $authenticator,
//                            $request
//                        );
//                    }
//                } elseif (!$muni) {
//
//                        $errorMessages['municipalite'] = 'Municipalité non valide ou non reconnue.';
//                    return $this->render('test/signup.html.twig', [
//                        'name' => $user ? $user->getFirstName() : '',
//                        'lastName' => $user ? $user->getLastName() : '',
//                        'email' => $user ? $user->getEmail() : '',
//                        'role' => $user ? $user->getRole() : '',
//                        'errors' => $errorMessages ,
//                    ]);
//
//                    }
//            }
//
//        } else {
//                return  $this->redirectToRoute('app_register');
//            }
//
//        return $this->render('test/signup.html.twig', [
//            'name' => $user ? $user->getFirstName() : '',
//            'lastName' => $user ? $user->getLastName() : '',
//            'email' => $user ? $user->getEmail() : '',
//            'role' => $user ? $user->getRole() : '',
//            'errors' => $errorMessages ,
//        ]);
//    }
//    #[Route('/register', name: 'app_register')]
//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository): Response
//    {
//        $user = $session->get('authenticatedUser');
//        $userData = $request->request->get('user');
//        $errors = [];
//        $errorMessages = [];
//
//        if ($request->isMethod('POST') && $userData !== null) {
//            if ($request->request->has('submit_button')) {
//                $user = new User();
//                $user->setFirstName($userData['firstName']);
//                $user->setLastName($userData['lastName']);
//                $user->setAddress($userData['address']);
//                $user->setEmail($userData['email']);
//                $user->setPassword($userData['password']);
//
//                $errors = $validator->validate($user, null, 'add');
//
//                foreach ($errors as $error) {
//                    $field = $error->getPropertyPath();
//                    $errorMessages[$field] = $error->getMessage();
//                }
//
//                $muni=$municipaliteRepository->findOneBy([ 'name' =>$geo->getMunicipalityFromAddress($userData['address'])]);
//                if (count($errors) === 0 && $userData['password'] === $userData['password1'] && $muni) {
//                    if ($muni !== null) {
//                        $hashedPassword = $userPasswordHasher->hashPassword(
//                            $user,
//                            $userData['password']
//                        );
//                        $user->setMunicipalite($muni);
//                        $user->setPassword($hashedPassword);
//                        $user->setRole("Citoyen");
//                        $user->setDate(new DateTime());
//                        $entityManager->persist($user);
//                        $entityManager->flush();
//                        return $userAuthenticator->authenticateUser(
//                            $user,
//                            $authenticator,
//                            $request
//                        );
//                    }
//                } elseif (!$muni) {
//
//                    $errorMessages['municipalite'] = 'Municipalité non valide ou non reconnue.';
//                    return $this->render('test/signup.html.twig', [
//                        'name' => $user ? $user->getFirstName() : '',
//                        'lastName' => $user ? $user->getLastName() : '',
//                        'email' => $user ? $user->getEmail() : '',
//                        'role' => $user ? $user->getRole() : '',
//                        'errors' => $errorMessages ,
//                    ]);
//
//                }
//            }
//
//        } else {
//            return  $this->redirectToRoute('app_login');
//        }
//
//        return $this->render('test/signup.html.twig', [
//            'name' => $user ? $user->getFirstName() : '',
//            'lastName' => $user ? $user->getLastName() : '',
//            'email' => $user ? $user->getEmail() : '',
//            'role' => $user ? $user->getRole() : '',
//            'errors' => $errorMessages ,
//        ]);
//    }

}

