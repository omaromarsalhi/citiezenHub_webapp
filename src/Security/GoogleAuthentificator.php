<?php
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GoogleAuthentificator extends SocialAuthenticator
{     use TargetPathTrait;
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $user;
    private $session;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router,SessionInterface $session)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->session=$session;
    }

    public function start(Request $request,AuthenticationException $authException = null)
    {
    return new RedirectResponse('/connect/google',
        Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_google_check';

    }
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }


    public function getCredentials(Request $request)
    {
         if (!$this->supports($request)) {
             return null;
         }
        return $this->fetchAccessToken($this->getGoogleClient());
    }


    public function getUser($credentials,UserProviderInterface $userProvider)
    {
        $GoogleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);
        $email = $GoogleUser->getEmail();
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);
        $this->user=$GoogleUser;

        if ($user) {


                    return $user;
                }
            return $user;


    }

    public function onAuthenticationFailure(Request $request,AuthenticationException $exception)
    {
//        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
//        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request,TokenInterface $token, string $providerKey)
    {
        $user = $token->getUser();
        if ($user) {

            return new RedirectResponse($this->router->generate('editProfile'));
        }
//        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
//            var_dump($targetPath);
//            return new RedirectResponse('addUser');
//        }


//        return new RedirectResponse($this->urlGenerator->generate('addUser'));
//        return new RedirectResponse($this->urlGenerator->generate('signup_route'));

            $this->session->set('authenticatedUser', $this->user);
            $targetUrl = $this->router->generate('app_register');
            return new RedirectResponse($targetUrl);

    }
}