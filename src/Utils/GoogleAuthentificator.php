<?php
namespace App\Utils;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface ;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleAuthentificator extends SocialAuthenticator
{  //lllllnn

    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function start(Request $request,AuthenticationException $authException = null)
    {
    return new RedirectResponse('/login');
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/connect/google/check ' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
return $this->fetchAccessToken($this->getGoogleClient());
    }
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    public function getUser($credentials,UserProviderInterface $userProvider)
    {
        $googleUser =$this->getGoogleClient();
        $email=$googleUser->getEmail();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {
//                    $user->setFirstName($googleUser->getName());
//                    $user->setLastName($userData['lastName']);
//                    $user->setAddress($userData['address']);
//                    $user->setEmail($userData['email']);
//                    $user->setFacebookId($facebookUser->getId());
//                    $this->entityManager->persist($user);
//                    $this->entityManager->flush();

                    return $user;
                }


    }

    public function onAuthenticationFailure(Request $request,AuthenticationException $exception)
    {
        // TODO: Implement onAuthenticationFailure() method.
    }

    public function onAuthenticationSuccess(Request $request,TokenInterface $token, string $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }
}