<?php
namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class GoogleController extends AbstractController
{    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
        {
        return $clientRegistry
        ->getClient('google')
        ->redirect();
        }
    #[Route('/connect/google/check', name: 'connect_google_check')]
        public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
        {
//            // ** if you want to *authenticate* the user, then
//            // leave this method blank and create a Guard authenticator
//            // (read below)
//
//            /** @var \KnpU\OAuth2ClientBundle\Client\Provider\googleClient $client */
//            $client = $clientRegistry->getClient('google');
//
//            try {
//                /** @var \League\OAuth2\Client\Provider\googleUser $user */
//                $user = $client->fetchUser();
//
//                // do something with all this new power!
//                  $name = $user->getFirstName();
////                var_dump($user);
//                return $this->render('test/signup.html.twig', [
//                    'user'=>$user,
//                ]);
////                return $this->redirectToRoute('addUser', [
////                    'user'=>$user,
////                ]);
////
////                $newUser = new User();
////                $newUser->setEmail($user->getEmail());
////                $newUser->setFirstName($user->getFirstName());
////                $newUser->setLastName($user->getLastName());
////                $em = $doc->getManager();
////                $em->persist($user);
////                $em->flush();
//                die;
//
//                // ...
//            } catch (IdentityProviderException $e) {
//                // something went wrong!
//                // probably you should return the reason to the user
//                var_dump($e->getMessage());
//
//                die;
//            }
////            if($user)
////            {
////                return $this->redirectToRoute('app_login',[
////                'user'=>$user,
////
////                ]);
////            }
///
die;
        }

}