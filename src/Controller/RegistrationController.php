<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MunicipaliteRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthanticatorAuthenticator;
use App\MyHelpers\GeocodingService;

;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager, SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator, MunicipaliteRepository $municipaliteRepository): Response
    {

        $geo = new GeocodingService();
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
                $user->setEmail($userData['email']);
                $user->setPassword($userData['password']);
                $user->setState(0);
                $errors = $validator->validate($user, null, 'add');
                foreach ($errors as $error) {
                    $field = $error->getPropertyPath();
                    $errorMessages[$field] = $error->getMessage();
                }
                if ($userRepository->count(['email' => $userData['email']]) !== 0) {
                    $field = 'Email';
                    $errorMessages[$field] = 'Mail already exists';
                } else {
                    if (count($errors) === 0 && $userData['password'] === $userData['password1']) {

                        $hashedPassword = $userPasswordHasher->hashPassword(
                            $user,
                            $userData['password']
                        );

                        $user->setPassword($hashedPassword);
                        $user->setRole("Citoyen");
                        $user->setDate(new \DateTime('now', new \DateTimeZone('Africa/Tunis')));
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




        return $this->render('user/signup.html.twig', [
            'name' => $parts[0] ?: ($user ? $user->getFirstName() : ''),
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $email ?: ($user ? $user->getEmail() : ''),
            'role' => $user ? $user->getRole() : '',
            'errors' => $errorMessages,
        ]);

    }

}

