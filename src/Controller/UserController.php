<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Cassandra\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Utils\MyTools;

use DateTime;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user',methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/login', name: 'login',methods: ['GET', 'POST'])]
    public function login(UserPasswordHasherInterface $passwordHasher ,UserRepository $rep, ManagerRegistry $doc, Request $req): Response
    {
        $userData = $req->get('user');
        if ($req->isMethod('POST')  && $userData !== null ) {
            $email = $userData['email'];
            $password = $userData['password'];
            $user=$rep->findOneBy([ 'email' =>$userData['email'] ]);
            if ($user) {
                if ($passwordHasher->isPasswordValid($user, $password)) {
                    return $this->render('user/profile.html.twig', [
                        'name' => $user->getFirstName(),
                        'lastname' => $user->getLastName(),
                        'email' => $user->getEmail(),
                    ]);
                }
            }
        }
        return $this->render('user/login.html.twig', []);
    }


    #[Route('/addUser', name: 'addUser')]
    public function addUser(ManagerRegistry $doc, Request $req,UserRepository $rep,UserPasswordHasherInterface $passwordHasher ): Response
    {
        $userData = $req->get('user');
        if ($req->isMethod('POST') && $userData !== null ) {
            $user=$rep->findOneBy([ 'email' =>$userData['email'] ]);
            if($user===null && $userData['password'] === $userData['password1']) {
                    $user = new User();
                    $user->setFirstName($userData['firstName']);
                    $user->setLastName($userData['lastName']);
                    $user->setAddress($userData['address']);
                    $user->setEmail($userData['email']);
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $userData['password']
                    );
                    $user->setPassword($hashedPassword);
                    $user->setRole("Citoyen");
                    $user->setDate(new DateTime());
                    $em = $doc->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirectToRoute('editProfile', [
                        'name' => $user->getFirstName(),
                        'lastname' => $user->getLastName(),
                        'email' => $user->getEmail(),
                        'address' => $user->getAddress(),
                        'role' => $user->getRole(),
                        'cin' => '',
                        'phoneNumber' => '',
                        'age' => '',
                        'status' => '',
                    ]);


            }
        }
        return $this->render('test/signup.html.twig', []);

    }



    #[Route('/editProfile', name: 'editProfile',methods: ['GET', 'POST'])]
    public function editUser(UserRepository $rep, ManagerRegistry $doc, Request $req): Response
    {   $user=$rep->findOneBy([ 'email' =>$req->query->get('email')]);
        if ($req->isXmlHttpRequest()) {

            $email =$req->get('email');
            $user = $rep->findOneBy([ 'email' => $email]);
            $name =$req->get('name');
            $lastname =$req->get('lastname');
            $role =$req->get('role');
            $age =$req->get('age');
            $gender =$req->get('gender');
            $status =$req->get('status');
            $cin =$req->get('cin');
            $phoneNumber =$req->get('phoneNumber');
            $fichierImage = $req->files->get('image');
            $user->setFirstName($name);
            $user->setLastName($lastname);
            $user->setAge($age);
            $user->setPhoneNumber($phoneNumber);
            $user->setCin($cin);
            $user->setRole($role);
//            $this->uploadImageAction($req,$mtTools,$user);
            $user->setImageFile($fichierImage);
            if($status==1)
             $user->setStatus("Married");
            else$user->setStatus("Single");
            $em = $doc->getManager();
            $em->persist($user);
            $em->flush();
            return $this->render('user/edit_profile.html.twig', [
//                'name' => $user->getFirstName(),
//                'lastname' => $user->getLastName(),
//                'email' => $user->getEmail(),
//                'address' => $user->getAddress(),
//                'role' => $user->getRole(),
//                'cin' => '',
//                'phoneNumber'=> '',
//                'age' => '',
//                'status' => '',
            ]);
        }
        return $this->render('user/edit_profile.html.twig', [
            'name' =>$user->getFirstName(),
            'lastname' =>$user->getLastName(),
            'email' => $user->getEmail(),
            'address' => $user->getEmail(),
            'role' => $user->getRole(),
            'cin' => $user->getCin(),
            'phoneNumber'=>$user->getPhoneNumber(),
            'age' =>$user->getAge(),
            'status' =>$user->getStatus(),
        ]);


    }


    #[Route('/profile', name: 'profile',methods: ['GET', 'POST'])]
    public function consulterProfile( Request $req): Response
    {
        $name =$req->get('i');
        $lastname =$req->get('j');
        return $this->render('user/profile.html.twig', [
            'name' =>$name ,
            'lastname' =>$lastname ,
            'email' => 'dddd',
        ]);
    }



}

