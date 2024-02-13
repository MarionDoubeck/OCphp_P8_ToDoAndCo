<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


/**
 * Authenticator for user login.
 *
 * This class handles user authentication for login functionality.
 */
class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';


    /**
     * Constructor for the UserAuthenticator class.
     *
     * @param UrlGeneratorInterface $urlGenerator The URL generator interface used for generating URLs.
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {

    }//end __construct()


    /**
     * Authenticates the user.
     *
     * This method is responsible for authenticating the user based on the provided credentials.
     *
     * @param Request $request The request containing user credentials.
     *
     * @return Passport The authentication passport.
     */
    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );

    }//end authenticate()


    /**
     * Handles successful authentication.
     *
     * This method is called when authentication is successful.
     *
     * @param Request        $request      The request.
     * @param TokenInterface $token        The authentication token.
     * @param string         $firewallName The firewall name.
     *
     * @return Response|null The response to send, or null.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_main'));

    }//end onAuthenticationSuccess()


    /**
     * Gets the URL to redirect to on login failure.
     *
     * This method returns the URL to redirect to when login fails.
     *
     * @param Request $request The request.
     *
     * @return string The login URL.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);

    }//end getLoginUrl()

}//end class
