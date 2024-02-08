<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\UsersAuthenticator;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for user-related actions.
 */
/* #[IsGranted('ROLE_ADMIN')] */
class UserController extends AbstractController
{
    /**
     * Displays the page to manage the users.
     *
     * @return Response
     */
    #[Route('/gestion_des_utilisateurs', name: 'user_management')]
    #[IsGranted('ROLE_ADMIN', message: "Espace réservé aux administrateurs.")]
    public function manageUsers(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $users = $entityManager->getRepository(Users::class)->findAll();
        return $this->render('user/manageUsers.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Displays the page to create an user.
     *
     * @return Response
     */
    #[Route('/gestion_des_utilisateurs/nouvel_utilisateur', name: 'app_register')]
    #[IsGranted('ROLE_ADMIN',  message: "Espace réservé aux administrateurs.")]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password.
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Set roles.
            $roles = $form->get('roles')->getData();
            if($roles){
                $user -> setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            }else{
                $user -> setRoles(['ROLE_USER']);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success','L\'utilisateur a bien été créé.');
            return $this->redirectToRoute('user_management');

        }

        return $this->render('user/create.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * Displays the page for editing user data.
     *
     * @return Response
     */
    #[Route('/gestion_des_utilisateurs/modifier/{username}', name: 'edit_user')]
    #[IsGranted('ROLE_ADMIN',  message: "Espace réservé aux administrateurs.")]
    public function edit(
        Users $userToEdit, 
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(UserFormType::class, $userToEdit);
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
            if($roles){
                $realRoles = ['ROLE_ADMIN', 'ROLE_USER'];
                if($roles !== $userToEdit->getRoles()){
                    $userToEdit -> setRoles($realRoles);
                }
            }else{
                $realRoles = ['ROLE_USER'];
                if($roles !== $userToEdit->getRoles()){
                    $userToEdit -> setRoles($realRoles);
                }
            }

            $entityManager->persist($userToEdit);
            $entityManager->flush();

            $this->addFlash('success','Le profil de ' . $userToEdit->getUsername() . ' a bien été modifié');
            return $this->redirectToRoute('user_management');
        }
        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'userToEdit' => $userToEdit,
            'userForm' => $form->createView(),
        ]);
        
    }

    /**
     * Displays the page for deleting user.
     *
     * @return Response
     */
    #[Route('/gestion_des_utilisateurs/supprimer/{username}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN',  message: "Espace réservé aux administrateurs.")]
    public function deleteUser(
        Users $userToDelete,
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $entityManager->remove($userToDelete);
        $entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur ' . $userToDelete->getUsername() . ' a été supprimé avec succès');
        return $this->redirectToRoute('user_management');
    }
}
