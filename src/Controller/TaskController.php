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
     * Displays the page to see the tasks.
     *
     * @return Response
     */
    #[Route('/tasks', name: 'task_list')]
    public function displayTaskListAction(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $Tasks = $entityManager->getRepository(Task::class)->findAll();
        return $this->render('task/list.html.twig', [
            'tasks' => $Tasks,
        ]);
    }

    /**
     * Create a new task
     *
     * @return Response
     */
    #[Route('/tasks/create', name: 'task_create')]
    #[IsGranted('ROLE_USER')]
    public function createTaskAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTimeImmutable);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Edit a task
     *
     * @return Response
     */
    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    #[IsGranted('ROLE_USER')]
    public function editTaskAction(Task $taskToEdit, Request $request, EntityManagerInterface $entityManager)
    {
        $connectedUser = $this->getUser();
        if($connectedUser === $taskToEdit->getAuthor()){
            $form = $this->createForm(TaskFormType::class, $taskToEdit);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                
                    $entityManager->persist($taskToEdit);
                    $entityManager->flush();
                    $this->addFlash('success', 'La tâche a bien été modifiée.');

                    return $this->redirectToRoute('task_list');
                
                
            }
            return $this->render('task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $taskToEdit,
            ]);
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé.e à modifier cette tâche');
            return $this->redirectToRoute('task_list');
        }
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
    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ["POST", "GET"])]
    #[IsGranted('ROLE_USER')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->toggle(!$task->isIsDone());
        $entityManager->flush();

        if ($task->isIsDone()) {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        }else{
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle())); 
        }

        return $this->redirectToRoute('task_list');
    }


    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteTaskAction(Task $taskToDelete,EntityManagerInterface $entityManager): RedirectResponse
    {
        $connectedUser = $this->getUser();
        if($connectedUser === $taskToDelete->getAuthor() || (in_array('ROLE_ADMIN', $connectedUser->getRoles()) && null === $taskToDelete->getAuthor())){
            $entityManager->remove($taskToDelete);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');

            return $this->redirectToRoute('task_list');
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé.e à supprimer cette tâche');
            return $this->redirectToRoute('task_list');
        }
    }
}
