<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\MunicipaliteRepository;
use App\Security\UserAuthanticatorAuthenticator;
use App\Utils\GeocodingService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository,MailerInterface $mailer): Response
    {
        $email = $request->query->get('email');
        $name = $request->query->get('name');
        $parts = explode(" ", $name);
        $user = $session->get('authenticatedUser');
        $userData = $request->request->get('user');
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
//                        $email->envoyerEmail();
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

        return $this->render('test/signup.html.twig', [
            'name' =>  $parts[0] ? $parts[0] : ($user ? $user->getFirstName() : ''),
//            'lastName' =>  $parts[1]? $parts[1] : ($user ? $user->getLastName() : ''),
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $email ? $email : ($user ? $user->getEmail() : ''),
            'role' => $user ? $user->getRole() : '',
            'errors' => $errorMessages,
        ]);

    }

}

