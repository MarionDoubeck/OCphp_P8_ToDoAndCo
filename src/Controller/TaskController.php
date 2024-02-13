<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Form\TaskFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{


    /**
     * Displays the page to see the to-do tasks.
     *
     * @param EntityManagerInterface $entityManager The entity manager to fetch tasks.
     *
     * @return Response The response containing the rendered list of to-do tasks.
     */
    #[Route('/tasks', name: 'todo_task_list')]
    public function displayTodoTaskListAction(
        EntityManagerInterface $entityManager,
    ): Response {
        $tasks = $entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->where('t.isDone = :isDone')
            ->setParameter('isDone', false)
            ->getQuery()
            ->getResult();
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);

    }//end displayTodoTaskListAction()


    /**
     * Displays the page to see the done tasks.
     *
     * @param EntityManagerInterface $entityManager The entity manager to fetch tasks.
     *
     * @return Response The response containing the rendered list of done tasks.
     */
    #[Route('/tasks/done', name: 'done_task_list')]
    public function displayDoneTaskListAction(
        EntityManagerInterface $entityManager,
    ): Response {
        $tasks = $entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->where('t.isDone = :isDone')
            ->setParameter('isDone', true)
            ->getQuery()
            ->getResult();
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);

    }//end displayDoneTaskListAction()


    /**
     * Creates a new task.
     *
     * @param Request                $request       The request object.
     * @param EntityManagerInterface $entityManager The entity manager to persist the task.
     *
     * @return Response The response containing the form to create a new task or a redirection to the to-do task list.
     */
    #[Route('/tasks/create', name: 'task_create')]
    #[IsGranted('ROLE_USER')]
    public function createTaskAction(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() === TRUE && $form->isValid() === TRUE) {
            $task->setAuthor($this->getUser());
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTimeImmutable);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('todo_task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);

    }//end createTaskAction()


    /**
     * Edits a task.
     *
     * @param Task                   $taskToEdit    The task entity to edit.
     * @param Request                $request       The request object.
     * @param EntityManagerInterface $entityManager The entity manager to persist the task changes.
     *
     * @return Response The response containing the form to edit the task or a redirection to the appropriate task list.
     */
    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    #[IsGranted('ROLE_USER')]
    public function editTaskAction(
        Task $taskToEdit,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $connectedUser = $this->getUser();
        if ($connectedUser === $taskToEdit->getAuthor()) {
            $form = $this->createForm(TaskFormType::class, $taskToEdit);
            $form->handleRequest($request);
            if ($form->isSubmitted() === TRUE && $form->isValid() === TRUE) {
                $entityManager->persist($taskToEdit);
                $entityManager->flush();
                $this->addFlash('success', 'La tâche a bien été modifiée.');

                if ($taskToEdit->isIsDone() === TRUE) {
                    return $this->redirectToRoute('done_task_list');
                } else {
                    return $this->redirectToRoute('todo_task_list');
                }

            }

            return $this->render('task/edit.html.twig', [
                                                         'form' => $form->createView(),
                                                         'task' => $taskToEdit,
                                                        ]
            );
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé.e à modifier cette tâche');
            if ($taskToEdit->isIsDone()) {
                return $this->redirectToRoute('done_task_list');
            } else {
                return $this->redirectToRoute('todo_task_list');

            }

        }// end if

    }//end editTaskAction()


    /**
     * Toggles the status of a task (done/undone).
     *
     * @Route('/status/{title}', name='toggle_task')
     *
     * @param Tasks                  $task          The task entity to toggle.
     * @param EntityManagerInterface $entityManager The entity manager for persisting changes.
     *
     * @return RedirectResponse Redirects to the appropriate task list view after toggling the task status.
     */
    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ["POST", "GET"])]
    #[IsGranted('ROLE_USER')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->toggle(!$task->isIsDone());
        $entityManager->flush();

        if ($task->isIsDone() === TRUE) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            return $this->redirectToRoute('done_task_list');
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle()));
            return $this->redirectToRoute('todo_task_list');
        }

    }//end toggleTaskAction()


    /**
     * Deletes a task.
     *
     * @param Task                   $taskToDelete  The task entity to delete.
     * @param EntityManagerInterface $entityManager The entity manager to remove the task.
     *
     * @return RedirectResponse Redirects to the to-do task list after deleting the task.
     */
    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteTaskAction(
        Task $taskToDelete,
        EntityManagerInterface $entityManager
    ): RedirectResponse {
        $connectedUser = $this->getUser();
        if ($connectedUser === $taskToDelete->getAuthor() || (in_array('ROLE_ADMIN', $connectedUser->getRoles()) === TRUE && null === $taskToDelete->getAuthor())) {
            $entityManager->remove($taskToDelete);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('todo_task_list');
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé.e à supprimer cette tâche');
            return $this->redirectToRoute('todo_task_list');
        }

    }//end deleteTaskAction()


}//end class
