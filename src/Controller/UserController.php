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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use App\Utils\MyTools;

use DateTime;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user',methods: ['GET', 'POST'])]
    public function index(): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
//    #[Route('/login', name: 'login',methods: ['GET', 'POST'])]
//    public function login(UserPasswordHasherInterface $passwordHasher ,UserRepository $rep, ManagerRegistry $doc, Request $req ,AuthenticationUtils $authenticationUtils): Response
//    {
//        $error=$authenticationUtils->getLastAuthenticationError();
//        $lastUsername=$authenticationUtils->getLastUsername();
//        return $this->render('user/login.html.twig',[
//
//        ]);
////        $userData = $req->get('user');
////        if ($req->isMethod('POST')  && $userData !== null ) {
////            $email = $userData['email'];
////            $password = $userData['password'];
////            $user=$rep->findOneBy([ 'email' =>$userData['email'] ]);
////            if ($user) {
////                if ($passwordHasher->isPasswordValid($user, $password)) {
////                    return $this->render('user/profile.html.twig', [
////                        'name' => $user->getFirstName(),
////                        'lastname' => $user->getLastName(),
////                        'email' => $user->getEmail(),
////                    ]);
////                }
////            }
////        }
////        return $this->render('user/login.html.twig', []);
//    }


    #[Route('/editProfile', name: 'editProfile',methods: ['GET', 'POST'])]
    public function editUser(UserRepository $rep, ManagerRegistry $doc, Request $req): Response
    {    $user=$rep->findOneBy([ 'email' =>$this->getUser()->getUserIdentifier()]);

        if ($req->isXmlHttpRequest()) {

            $email =$req->get('email');
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
            $user->setStatus($status);
            $user->setGender($gender);
            $user->setImageFile($fichierImage);
            $user->serialize();
            $em = $doc->getManager();

            $em->persist($user);
            $em->flush();
            var_dump($user);


            return $this->render('user/edit_profile.html.twig', [
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
            ]);
        }
        return $this->render('user/edit_profile.html.twig', [
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
        ]);


    }
    #[Route('/editImage', name: 'editImage',methods: ['GET', 'POST'])]
    public function editUserImage(UserRepository $rep, ManagerRegistry $doc, Request $req): Response
    {    $user=$rep->findOneBy([ 'email' =>$this->getUser()->getUserIdentifier()]);
        if ($req->isXmlHttpRequest()) {
            $fichierImage = $req->files->get('imagee');
            $user->setImageFile($fichierImage);
            $em = $doc->getManager();
            $em->persist($user);
            $em->flush();
//            return $this->render('user/edit_profile.html.twig', [
//                'name' =>$user->getFirstName(),
//                'lastname' =>$user->getLastName(),
//                'email' => $user->getEmail(),
//                'address' => $user->getEmail(),
//                'role' => $user->getRole(),
//                'cin' => $user->getCin(),
//                'phoneNumber'=>$user->getPhoneNumber(),
//                'age' =>$user->getAge(),
//                'status' =>$user->getStatus(),
//                'gender' =>$user->getGender(),
//                'image'=>$user->getImage(),
//            ]);
            return new Response(' supprimé avec succès false ', Response::HTTP_OK);
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
            'gender' =>$user->getGender(),
            'image'=>$user->getImage(),
        ]);


    }
    #[Route('/delete', name: 'app_user_delete')]
    public function delete(ManagerRegistry $doctrine, UserRepository $userRepository, Request $req): Response
    {
            $user=$userRepository->findOneBy([ 'email' =>$this->getUser()->getUserIdentifier()]);
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();
            return $this->redirectToRoute('app_login');
//        return new Response(' supprimé avec succès false ', Response::HTTP_OK);

    }


    #[Route('/profile', name: 'profile',methods: ['GET', 'POST'])]
    public function consulterProfile( Request $req): Response
    {
        $name =$req->get('name');
        $lastname =$req->get('lastname');
        $email =$req->get('email');
        $image =$req->get('image');
        return $this->render('user/profile.html.twig', [
            'name' =>$name ,
            'lastname' =>$lastname ,
            'email' => $email,
            'image'=>$image,
        ]);
    }
    #[Route('/changePassword', name: 'changePassword',methods: ['GET', 'POST'])]
    public function changePassword(UserPasswordHasherInterface $userPasswordHasher,ManagerRegistry $doctrine, UserRepository $userRepository, Request $req)
    {
        $user=$userRepository->findOneBy([ 'email' =>$this->getUser()->getUserIdentifier()]);
        $newPassword =$req->get('newPassword');
        $confirmPassword =$req->get('confirmPassword');
        $oldPassword = $userPasswordHasher->hashPassword(
            $user,
            $req->get('oldPassword')
        );
        if(strcmp($user->getPassword(),$oldPassword))
        {
          if(strcmp($newPassword,$confirmPassword))
          {
              $hashedPassword = $userPasswordHasher->hashPassword(
                  $user,
                  $newPassword
              );
              $user->setPassword($hashedPassword);

          }

        }

    }


}

