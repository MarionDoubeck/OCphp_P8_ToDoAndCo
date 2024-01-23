<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\EditUserFormType;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Controller for user-related actions.
 */
class UserController extends AbstractController
{


    /**
     * Displays the index page of the user profile.
     *
     * @return Response
     */
    #[Route('/mon-profil-utilisateur/{username}', name: 'user_profile')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'Profil de l\'utilisateur',
        ]);
    }

    #[Route('/mon-profil-utilisateur/{username}/modifier', name: 'edit_user')]
    /**
     * Displays the page for editing user data.
     *
     * @return Response
     */
    public function edit(
        Users $userToEdit, 
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        UsersAuthenticator $authenticator, 
        EntityManagerInterface $entityManager,
        TokenInterface $tokenStorage // Dependency injection for TokenStorageInterface ; if omitted edit will logout the user.
    ): Response
    {
        $form = $this->createForm(EditUserFormType::class, $userToEdit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('username')->getData() && $form->get('username')->getData() !== $userToEdit->getUsername()){
                $userToEdit->setUsername($form->get('username')->getData());
            }
            if($form->get('email')->getData() && $form->get('email')->getData() !== $userToEdit->getEmail()){
                $userToEdit->setEmail($form->get('email')->getData());
            }
            if($form->get('plainPassword')->getData()){
                // Encode the plain password.
                $userToEdit->setPassword(
                    $userPasswordHasher->hashPassword(
                        $userToEdit,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            // Set roles.
            $roles = $form->get('roles')->getData();
            if($roles !== $userToEdit->getRoles()){
                if($roles){
                    $userToEdit -> setRoles(['ROLE_ADMIN', 'ROLE_USER']);
                }else{
                    $userToEdit -> setRoles(['ROLE_USER']);
                }
            }

            $entityManager->persist($userToEdit);
            $entityManager->flush();

            $this->addFlash('success','Votre profil a bien été modifié');
            return $this->redirectToRoute('user_profile', ['username' => $userToEdit->getUsername(),'_fragment' => 'flash']);
        }
        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $userToEdit,
            'editUserForm' => $form->createView(),
        ]);
        
    }
}
