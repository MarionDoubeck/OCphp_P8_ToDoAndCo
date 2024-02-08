<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
/* use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted; */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\UserAuthenticator;
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
     * Displays the page to manage the User.
     *
     * @return Response
     */
    #[Route('/users', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function displayUserListAction(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $Users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/list.html.twig', [
            'users' => $Users,
        ]);
    }

    /**
     * Displays the page to create an user.
     *
     * @return Response
     */
    #[Route('/users/create', name: 'user_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUserAction(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password.
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
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
            return $this->redirectToRoute('user_list');

        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays the page for editing user data.
     *
     * @return Response
     */
    #[Route('/users/{id}/edit', name: 'user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUserAction(
        User $userToEdit, 
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
            if($form->get('password')->getData()){
                // Encode the password.
                $userToEdit->setPassword(
                    $userPasswordHasher->hashPassword(
                        $userToEdit,
                        $form->get('password')->getData()
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
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $userToEdit,
            'form' => $form->createView(),
        ]);
        
    }
}