<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthanticatorAuthenticator;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator): Response
    {
        $user = $session->get('authenticatedUser');
        $userData = $request->request->get('user');
        $errors = [];
        if ($request->isMethod('POST') && $userData !== null) {
            if ($request->request->has('submit_button')) {
                $user = new User();
                $user->setFirstName($userData['firstName']);
                $user->setLastName($userData['lastName']);
                $user->setAddress($userData['address']);
                $user->setEmail($userData['email']);
                $user->setPassword($userData['password']);
                $errors = $validator->validate($user);
                if (count($errors) === 0 && $userData['password'] === $userData['password1']) {
                    $hashedPassword = $userPasswordHasher->hashPassword(
                        $user,
                        $userData['password']
                    );
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
            } else {
                return  $this->redirectToRoute('app_login');
            }
        }
        return $this->render('test/signup.html.twig', [
            'name' => $user ? $user->getFirstName() : '',
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $user ? $user->getEmail() : '',
            'role' => $user ? $user->getRole() : '',
            'errors' => $errors,
        ]);
    }
//    #[Route('/register', name: 'app_register')]
//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator): Response
//    {   $user = $session->get('authenticatedUser');
//        $userData = $request->request->get('user');
//        if ($request->isMethod('POST') && $userData !== null) {
//            if ($request->request->has('submit_button')) {
////            $user = $request->findOneBy(['email' => $userData['email']]);
//                $errors = $validator->validate($user);
//
//                if (count($errors) === 0 && $userData['password'] === $userData['password1']) {
//                    $user = new User();
//                    $user->setFirstName($userData['firstName']);
//                    $user->setLastName($userData['lastName']);
//                    $user->setAddress($userData['address']);
//                    $user->setEmail($userData['email']);
//                    $hashedPassword = $userPasswordHasher->hashPassword(
//                        $user,
//                        $userData['password']
//                    );
//                    $user->setPassword($hashedPassword);
//                    $user->setRole("Citoyen");
//                    $user->setDate(new DateTime());
//                    $em = $entityManager;
//                    $em->persist($user);
//                    $em->flush();
//                    return $userAuthenticator->authenticateUser(
//                        $user,
//                        $authenticator,
//                        $request
//                    );
//                }
//            }
//            else
//                return  $this->redirectToRoute('app_login');
//        }
//        if($user)
//        {
//            return $this->render('test/signup.html.twig', [
//                'name'=>$user->getFirstName(),
//                'lastName'=>$user->getLastName(),
//                'email' => $user->getEmail(),
//                'errors' => $errors ?? null,
//            ]);
//        }
//        return $this->render('test/signup.html.twig', [
//            'name'=>'',
//            'lastName'=>'',
//            'email' =>'',
//            'errors' => $errors ?? null, // Passer les erreurs de validation au template Twig
//        ]);
//    }
}

