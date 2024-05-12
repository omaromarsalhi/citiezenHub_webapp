<?php

namespace App\Controller;

use App\Entity\Municipalite;
use App\Entity\User;
use App\MyHelpers\ImageHelperUser;
use App\Repository\MunicipaliteRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthanticatorAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminControlllerController extends AbstractController
{
    #[Route('/admin/controlller', name: 'app_admin_controlller')]
    public function index(): Response
    {
        return $this->render('admin/settings.html.twig');
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
    public function AddMunicipality(MunicipaliteRepository $rep, ValidatorInterface $validator, ManagerRegistry $doc, Request $req, ImageHelperUser $imageHelper): Response
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
        return new Response('done');
    }

    #[Route('/registerAdmin', name: 'app_register_Admin')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthanticatorAuthenticator $authenticator, EntityManagerInterface $entityManager,SessionInterface $session, ValidatorInterface $validator, TranslatorInterface $translator,GeocodingService $geo,MunicipaliteRepository $municipaliteRepository,MailerInterface $mailer): Response
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
                $user->setLastName($userData['lastName']);
                $user->setAddress($userData['address']);
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

        return $this->render('admin/sign-up.html.twig', [
            'name' => $user ? $user->getFirstName() : '',
            'lastName' => $user ? $user->getLastName() : '',
            'email' => $user ? $user->getEmail() : '',
            'address' => $user ? $user->getAddress() : '',
            'errors' => $errorMessages,
        ]);

    }
    #[Route(path: '/loginAdmin', name: 'app_login_Admin')]
    public function login(AuthenticationUtils $authenticationUtils ): Response
    {

//         if ($this->getUser()) {
//             var_dump($this->getUser());
////             return $this->redirectToRoute('target_path');
//         }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('admin/sign-in.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route('/editProfileAdmin', name: 'editProfileAdmin',methods: ['GET', 'POST'])]
    public function editUser(UserRepository $rep, ManagerRegistry $doc, Request $req,ValidatorInterface $validator,ImageHelperUser $imageHelper,SessionInterface $session): Response
    {
     /*   $user=$rep->findOneBy([ 'email' =>$this->getUser()->getUserIdentifier()]);
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
            $phoneNumber = $req->get('phoneNumber');
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
            if($fichierImage!=null)
                $user->setImage($imageHelper->saveImages($fichierImage));
            $datee = date_create($date);
            $user->setDob($datee);
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
            ],422);

        }

        return $this->render('admin/settings.html.twig', [
            'name' =>$user->getFirstName(),
            'lastname' =>$user->getLastName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'role' => $user->getRole(),
            'cin' => $user->getCin(),
            'phoneNumber'=>$user->getPhoneNumber(),
            'age' =>$user->getAge(),
            'status' =>$user->getStatus(),
            'image'=>$user->getImage(),
            'gender'=>$user->getGender(),
            'dob'=>$user->getDob(),
            'errors' => $errorMessages,

        ]);*/


        return new Response('daaa');
    }


}
