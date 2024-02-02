<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TaskFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Controller for task-related actions.
 */
class TaskController extends AbstractController
{
    /**
     * Displays the page to manage the users.
     *
     * @return Response
     */
    #[Route('/liste_des_taches', name: 'todolist')]
    public function todoList(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $tasksToDo = $entityManager->getRepository(Tasks::class)->findTasksNotDone();
        return $this->render('task/todolist.html.twig', [
            'tasksToDo' => $tasksToDo,
        ]);
    }

    #[Route('/liste_des_taches_terminees', name: 'donelist')]
    public function doneList(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $doneTasks = $entityManager->getRepository(Tasks::class)->findTasksDone();
        return $this->render('task/donelist.html.twig', [
            'doneTasks' => $doneTasks,
        ]);
    }

    #[Route('/nouvelle_tache', name: 'create_task')]
    public function createTask(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        $task = new Tasks;
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
        }
        return $this->render('task/create.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    #[Route('/modifier/{title}', name: 'edit_task')]
    public function edit(
        Tasks $taskToEdit, 
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $form = $this->createForm(TaskFormType::class, $taskToEdit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taskToEdit);
            $entityManager->flush();

            $this->addFlash('success', $taskToEdit->getTitle() . ' a bien été modifié');
            return $this->redirectToRoute('todolist');
        }
        return $this->render('task/edit.html.twig', [
            'taskToEdit' => $taskToEdit,
            'taskForm' => $form->createView(),
        ]);
    }


    /**
     * Toggles the status of a task (done/undone).
     *
     * @Route('/status/{title}', name='toggle_task')
     *
     * @param Tasks                  $task           The task entity to toggle.
     * @param EntityManagerInterface $entityManager The entity manager for persisting changes.
     *
     * @return RedirectResponse Redirects to the appropriate task list view after toggling the task status.
     */
    #[Route('/status/{title}', name: 'toggle_task', methods: ["POST", "GET"])]
    public function toggle(
        Tasks $task,
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $task->toggle(!$task->isDone());
        $entityManager->flush();
        $this->addFlash('success', 'le status de ' . $task->getTitle() . ' a été modifié avec succès');
        if ($task->isDone()) {
            return $this->redirectToRoute('todolist');
        } else {
            return $this->redirectToRoute('donelist');
        }
    }

    #[Route('/supprimer/{title}', name: 'delete_task')]
    public function delete(
        Tasks $taskToDelete,
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $connectedUser = $this->getUser();
        if($connectedUser === $taskToDelete->getAuthor() || (in_array('ROLE_ADMIN', $connectedUser->getRoles()) && 'Anonymus' === $taskToDelete->getAuthor()->getUsername())){
            $entityManager->remove($taskToDelete);
            $entityManager->flush();
            $this->addFlash('success', $taskToDelete->getTitle() . ' a été supprimé avec succès');
            return $this->redirectToRoute('todolist');
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé.e à supprimer cette tâche');
            return $this->redirectToRoute('todolist');
        }
    }
}