<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    /**
     * Renders the login page.
     *
     * This method is responsible for rendering the login page of the application.
     * It takes an AuthenticationUtils object as a parameter.
     *
     * @param AuthenticationUtils $authenticationUtils The authentication utils to handle login errors and last username.
     *
     * @return Response The response containing the rendered login page.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }//end login


    /**
     * Handles user logout.
     *
     * This method is responsible for handling user logout.
     * It does not take any parameters.
     *
     * @return void
     *
     * @throws \LogicException This method can be blank - it will be intercepted by the logout key on your firewall.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }//end logout()


}
