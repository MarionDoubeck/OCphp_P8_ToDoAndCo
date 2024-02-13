<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{

    public function testListWhenLoggedInAsUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();

        $client->request(Request::METHOD_GET, '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('task_list');
    }

 
    public function testDisplayCreateWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/tasks/create');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testDisplayCreateWhenLoggedIn(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();

        $crawler = $client->request(Request::METHOD_GET, '/tasks/create');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('task_create');
        $this->assertInstanceOf(Form::class, $crawler->selectButton('Ajouter')->form());
    }

    public function testCreateTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();

        $crawler = $client->request(Request::METHOD_GET, '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $randomName = bin2hex(random_bytes(10));
        $form['task_form[title]'] = 'newTask'.$randomName;
        $form['task_form[content]'] = 'My task content';
        $client->submit($form);

        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('div.alert.alert-success');
    }


    public function testEditTaskWhenNotLoggedIn(): void
    {
        $client = static::createClient();
        $task = static::getContainer()->get(TaskRepository::class)->findOneByTitle('taskToEdit');
        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/edit');

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }


    public function testEditTaskAsAuthor(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('authorOfTasks');
        $client->loginUser($testUser);
        $client->followRedirects();

        $task = static::getContainer()->get(TaskRepository::class)->findOneByTitle('taskToEdit');
        $crawler = $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/edit');

        $this->assertSelectorExists(
            'button[type="submit"].btn.btn-success.pull-right',
            'Modifier'
        );
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('textarea#task_form_title[name="task_form[title]"]', $task->getTitle());
        $this->assertSelectorTextSame('textarea#task_form_content[name="task_form[content]"]', $task->getContent());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task_form[title]'] = 'Modified Title';
        $form['task_form[content]'] = 'My modified task content';
        $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', 'Superbe ! La tâche a bien été modifiée.');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task_form[title]'] = 'taskToEdit';
        $form['task_form[content]'] = 'My task content';
        $client->submit($form);
    }


    public function testEditTaskAsNotAuthor(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();

        $task = static::getContainer()->get(TaskRepository::class)->findOneByTitle(['taskToEdit']);
        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/edit');

        $this->assertSelectorTextContains('.alert.alert-danger[role="alert"]', 'Oops ! Vous n\'êtes pas autorisé.e à modifier cette tâche');

    }


    public function testTaskToggle(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();
        
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskToDisplay');
        $isDone = $task->isIsDone();
        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/toggle');

        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('div.alert.alert-success');
        $this->assertSame(!$isDone, $task->isIsDone());
    }


    public function testTaskToggleReverse(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();
        
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskToDisplay');
        $isDone = $task->isIsDone();
        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/toggle');

        $this->assertRouteSame('task_list');
        $this->assertSelectorExists('div.alert.alert-success');
        $this->assertSame(!$isDone, $task->isIsDone());
    }

    
    public function testTaskDeleteAsNotAuthor(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskToDelete');

        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');
        $this->assertSelectorTextContains('.alert.alert-danger[role="alert"]', 'Oops ! Vous n\'êtes pas autorisé.e à supprimer cette tâche');

    }


    public function testTaskDeleteAsAuthor(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('authorOfTasks');
        $client->loginUser($testUser);
        $client->followRedirects();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskToDelete');

        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');
        $this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', 'Superbe ! La tâche a bien été supprimée.');

        $crawler = $client->request(Request::METHOD_GET, '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task_form[title]'] = 'taskToDelete';
        $form['task_form[content]'] = 'My task to delete content';
        $client->submit($form);
    }

    
    public function testAnonymousTaskDeleteAsNotAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('userToLog');
        $client->loginUser($testUser);
        $client->followRedirects();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskAnonymusToDelete');

        $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');
        $this->assertSelectorTextContains('.alert.alert-danger[role="alert"]', 'Oops ! Vous n\'êtes pas autorisé.e à supprimer cette tâche');
    }


    public function testAnonymousTaskDeleteAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('admin');
        $client->loginUser($testUser);
        $client->followRedirects();

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneByTitle('taskAnonymusToDelete');

        $crawler = $client->request(Request::METHOD_GET, '/tasks/' . $task->getId() . '/delete');
        //$this->assertRouteSame('task_list');
        $this->assertSelectorTextContains('.alert.alert-success[role="alert"]', 'Superbe ! La tâche a bien été supprimée.');

        //logout
        $tokenStorage = static::getContainer()->get('security.token_storage');
        $tokenStorage->setToken(null);
        $crawler = $client->request(Request::METHOD_GET, '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task_form[title]'] = 'taskAnonymusToDelete';
        $form['task_form[content]'] = 'My task without author';
        $client->submit($form);
    }


}