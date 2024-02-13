<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
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
class UserController extends AbstractController
{


    /**
     * Displays the page to manage users.
     *
     * @param EntityManagerInterface $entityManager The entity manager to fetch users.
     *
     * @return Response The response containing the rendered list of users.
     */
    #[Route('/users', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function displayUserListAction(
        EntityManagerInterface $entityManager,
    ) : Response {
        $Users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('user/list.html.twig', ['users' => $Users]);

    }//end displayUserListAction()


    /**
     * Displays the page to create a user.
     *
     * @param Request $request The request object.
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher service.
     * @param UserAuthenticatorInterface $userAuthenticator The user authenticator service.
     * @param UserAuthenticator $authenticator The authenticator service.
     * @param EntityManagerInterface $entityManager The entity manager to persist the user.
     *
     * @return Response The response containing the form to create a user or a redirection to the user list.
     */
    #[Route('/users/create', name: 'user_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function createUserAction(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() === TRUE && $form->isValid() === TRUE) {
            // Encode the password.
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // Set roles.
            $roles = $form->get('roles')->getData();
            $user -> setRoles(['ROLE_USER']);
            if ($roles !== FALSE) {
                $user -> setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success','L\'utilisateur a bien été créé.');
            return $this->redirectToRoute('user_list');
        }//end if

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);

    }//end createUserAction()


    /**
     * Displays the page for editing user data.
     *
     * @param User                        $userToEdit         The user entity to edit.
     * @param Request                     $request            The request object.
     * @param UserPasswordHasherInterface $userPasswordHasher The password hasher service.
     * @param EntityManagerInterface      $entityManager      The entity manager to persist the user changes.
     *
     * @return Response The response containing the form to edit the user or a redirection to the user list.
     */
    #[Route('/users/{id}/edit', name: 'user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUserAction(
        User $userToEdit,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(UserFormType::class, $userToEdit);
        $form->handleRequest($request);

        if ($form->isSubmitted() === TRUE && $form->isValid() === TRUE) {
            if ($form->get('username')->getData() !== NULL && $form->get('username')->getData() !== $userToEdit->getUsername()) {
                $userToEdit->setUsername($form->get('username')->getData());
            }

            if ($form->get('email')->getData() !== NULL && $form->get('email')->getData() !== $userToEdit->getEmail()) {
                $userToEdit->setEmail($form->get('email')->getData());
            }

            if ($form->get('password')->getData() !== NULL) {
                $userToEdit->setPassword(
                    $userPasswordHasher->hashPassword(
                        $userToEdit,
                        $form->get('password')->getData()
                    )
                );
            }

            $roles = $form->get('roles')->getData();
            if ($roles !== FALSE) {
                $realRoles = ['ROLE_ADMIN', 'ROLE_USER'];
                if ($roles !== $userToEdit->getRoles()) {
                    $userToEdit -> setRoles($realRoles);
                }

            } else {
                $realRoles = ['ROLE_USER'];
                if ($roles !== $userToEdit->getRoles()) {
                    $userToEdit -> setRoles($realRoles);
                }

            }

            $entityManager->persist($userToEdit);
            $entityManager->flush();

            $this->addFlash('success','Le profil de '.$userToEdit->getUsername().' a bien été modifié');
            return $this->redirectToRoute('user_list');
        }//end if
        return $this->render('user/edit.html.twig', [
                                                        'controller_name' => 'UserController',
                                                        'user' => $userToEdit,
                                                        'form' => $form->createView(),
                                                    ]
        );

    }//end editUserAction()


    /**
     * Deletes a user.
     *
     * @param User                   $userToDelete  The user entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the user and associated tasks.
     *
     * @return RedirectResponse Redirects to the user list after deleting the user.
     */
    #[Route('/users/{id}/delete', name: 'user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUserAction(
        User $userToDelete,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $tasksToDelete = $userToDelete->getTasks();
        foreach ($tasksToDelete as $task) {
            $entityManager->remove($task);
        }
        $entityManager->remove($userToDelete);
        $entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur '.$userToDelete->getUsername().' a été supprimé avec succès');
        return $this->redirectToRoute('user_list');

    }//end deleteUserAction()


}
