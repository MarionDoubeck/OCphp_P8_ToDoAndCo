<?php

namespace App\Controller;

use App\Entity\Tasks;
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

    #[Route('/supprimer/{title}', name: 'delete_task')]
    public function delete(
        Tasks $taskToDelete,
        EntityManagerInterface $entityManager
    ): RedirectResponse
    {
        $entityManager->remove($taskToDelete);
        $entityManager->flush();
        $this->addFlash('success', $taskToDelete->getTitle() . ' a été supprimé avec succès');
        return $this->redirectToRoute('todolist');
    }
}