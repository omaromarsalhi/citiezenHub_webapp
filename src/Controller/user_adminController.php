<?php

namespace App\Controller;

use App\Entity\Municipalite;
use App\Entity\User;
use App\Repository\MunicipaliteRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthanticatorAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class user_adminController extends AbstractController
{
    #[Route('/munisipalite', name: 'app_munisipalite')]
    public function index(MunicipaliteRepository $mun, UserRepository $repUser): Response
    {
        // Récupérer toutes les municipalités
        $municipalites = $mun->findAll();

        $municipalitesByGouvernement = [];

        foreach ($municipalites as $municipalite) {
            $gouvernement = $municipalite->getGoverment();

            if (!isset($municipalitesByGouvernement[$gouvernement])) {
                $municipalitesByGouvernement[$gouvernement] = [];
            }

            $municipalitesByGouvernement[$gouvernement][] = [
                'id' => $municipalite->getId(),
                'name' => $municipalite->getName(),
                'address' => $municipalite->getAddress(),
                'size' => count($repUser->findBy(['municipalite' => $municipalite->getId()])),
                'user'=> $repUser->findBy(['municipalite' => $municipalite->getId()]),
            ];
        }


        return $this->render('user/user_admin.html.twig', [
            'muni' => $municipalitesByGouvernement,

        ]);
    }

    #[Route('/Show', name: 'Show')]
    public function Show(UserRepository $rep): Response
    {
        $user = $rep->findAll();
        return $this->render('user/customers.html.twig', [
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
            return $this->render('user/customer-details.html.twig', [
                'user' => $user,
                'subscriptionPeriod' => $diffyears,
            ]);
        }
        if ($diffMonths !== 0) {
            $diffMonths = sprintf("%d months", $diffMonths);
            return $this->render('user/customer-details.html.twig', [
                'user' => $user,
                'subscriptionPeriod' => $diffMonths,
            ]);

        }

        return $this->render('user/customer-details.html.twig', [
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

    #[Route('/registerAdmin', name: 'app_register_Admin')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator): Response
    {
//        $email = $request->query->get('email');
//        $name = $request->query->get('name');
//        $parts = explode(" ", $name);
        $user = $session->get('authenticatedUser');
        $userData = $request->request->get('admin');
        $errors=0;
        $errorMessages = [];
        if ($request->isMethod('POST') && $userData !== null) {
            $user = new User();
            $user->setFirstName($userData['firstName']);
//                $user->setLastName($userData['lastName']);
//                $user->setAddress($userData['address']);
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $errors = $validator->validate($user, null, 'add');
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $errorMessages[$field] = $error->getMessage();
            }
            if (count($errors) === 0 && $userData['password'] === $userData['confirmPassword']) {
                $hashedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $userData['password']
                );
                $user->setPassword($hashedPassword);
                $user->setRole("Admin");
//                        $user->setDate(new DateTime());
                $user->setState(1);
                $entityManager->persist($user);
                $entityManager->flush();
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );

            }


        }

        return $this->render('user/sign-up.html.twig', [
            'name' => $user ? $user->getFirstName() : '',
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $user ? $user->getEmail() : '',
            'address' => $user ? $user->getAddress() : '',
            'errors' => $errorMessages,
        ]);

    }

    #[Route('/editProfileAdmin', name: 'editProfileAdmin', methods: ['GET', 'POST'])]
    public function editUser(UserRepository $rep, ManagerRegistry $doc, Request $req, ValidatorInterface $validator, ImageHelper $imageHelper, SessionInterface $session): Response
    {
        $user = $rep->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $errorMessages = [];
        if ($req->isXmlHttpRequest()) {
            $email = $req->get('email');
            $name = $req->get('name');
            $lastname = $req->get('lastname');
            $role = $req->get('role');
            $age = $req->get('age');
            $gender = $req->get('gender');
            $status = $req->get('status');
            $cin = $req->get('cin');
            $phoneNumber = $req->get('phone');

            $date = $req->get('date');
            $fichierImage = $req->files->get('image');
            $user->setFirstName($name);
            $user->setLastName($lastname);
            $user->setAge($age);
            $user->setPhoneNumber($phoneNumber);
            $user->setCin($cin);
            $user->setRole($role);
            $user->setStatus($status);
            $user->setGender($gender);
            if ($fichierImage != null)
                $user->setImage($imageHelper->saveImages($fichierImage));

            $errors = $validator->validate($user, null, 'creation');
            foreach ($errors as $error) {
                $field = $error->getPropertyPath();
                $errorMessages[$field] = $error->getMessage();
                var_dump($field);
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
                        'role' => $user->getRole(),
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

        }

        return $this->render('user/profile.html.twig', [
            'name' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'role' => $user->getRole(),
            'cin' => $user->getCin(),
            'phoneNumber' => $user->getPhoneNumber(),
            'age' => $user->getAge(),
            'status' => $user->getStatus(),
            'image' => $user->getImage(),
            'gender' => $user->getGender(),
            'dob' => $user->getDob(),
            'errors' => $errorMessages,

        ]);


    }

    #[Route('/ShowDetailsMunicipality/{id}', name:'Show_Details_Municipality')]
    public function ShowDetailsMunicipality($id, MunicipaliteRepository $rep, UserRepository $repUser): Response
    {
        $municipality = $rep->find($id);
        $user = $repUser->findBy(['municipalite' => $id]);
        return $this->render('user/customers.html.twig', [
            'list' => $user,
        ]);

    }




}



