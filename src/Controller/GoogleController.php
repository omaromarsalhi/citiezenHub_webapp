<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    public function connectAction(ClientRegistry $clientRegistry)
        {
        return $clientRegistrye
        ->getClient('google')
        ->redirect();
        }
    #[Route('/connect/google/check', name: 'connect_google_check')]
        public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
        {
            if(!$this->getUser()){
                return new JsonResponse(array('status'=> false,'message'=>"user not found "));
            }else{
                return $this->redirectToRoute('login');
            }
        $client = $clientRegistry->getClient('google');


//        try {
//            // the exact class depends on which provider you're using
//            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
//            $user = $client->fetchUser();
//
//            // do something with all this new power!
//            // e.g. $name = $user->getFirstName();
//            var_dump($user); die;
//            // ...
//        } catch (IdentityProviderException $e) {
//            // something went wrong!
//            // probably you should return the reason to the user
//            var_dump($e->getMessage()); die;
//        }
    }

}