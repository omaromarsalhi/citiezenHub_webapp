<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthanticatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const ADMIN_LOGIN_ROUTE = 'app_login_Admin'; // Nouveau chemin de connexion
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public function supports(Request $request): bool
    {
        return in_array($request->attributes->get('_route'), [self::LOGIN_ROUTE, self::ADMIN_LOGIN_ROUTE])
            && $request->isMethod('POST');
//        return self::LOGIN_ROUTE === $request->attributes->get('_route')
//            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );


    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            return new RedirectResponse($targetPath);
        }
        if($user->getRole()=='Citoyen')
        return new RedirectResponse($this->urlGenerator->generate('editProfile'));
        else
            return new RedirectResponse($this->urlGenerator->generate('editProfileAdmin'));

    }

    protected function getLoginUrl(Request $request): string
    {
//        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
        if ($request->attributes->get('_route') === self::ADMIN_LOGIN_ROUTE) {
            return $this->urlGenerator->generate(self::ADMIN_LOGIN_ROUTE);
        }

        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

}
