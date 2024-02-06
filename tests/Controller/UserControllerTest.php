<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\Util\ClassUtils;


class UserControllerTest extends WebTestCase
{
    /**
     * Test case to check if the user management page is accessible.
     */
    public function testUserManagementPage()
    {
        $client = static::createClient();
        $client->request('GET', '/gestion_des_utilisateurs');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Gestion des utilisateurs');
    }

    /**
     * Test case to check if the new user registration page is accessible.
     */
    public function testNewUserRegistrationPage()
    {
        $client = static::createClient();
        $client->request('GET', '/gestion_des_utilisateurs/nouvel_utilisateur');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
    }

    /**
     * Test case to check if a new user can be successfully registered.
     */
    public function testNewUserRegistration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/gestion_des_utilisateurs/nouvel_utilisateur');

        $form = $crawler->selectButton('Inscription')->form();

        // Fill in the form fields with the necessary data
        $form['user_form[username]'] = 'test_user';
        $form['user_form[plainPassword][first]'] = 'password123';
        $form['user_form[plainPassword][second]'] = 'password123';
        $form['user_form[email]'] = 'email@test.ts';
        
        $client->submit($form);

        $this->assertResponseRedirects('/gestion_des_utilisateurs');
        $client->followRedirect();

        $this->assertSelectorTextContains('.flash-message', 'L\'utilisateur a bien été créé.');
    }


    /**
     * Helper method to create a test user.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \App\Entity\Users
     */
    private function createTestUser($entityManager)
    {
        $user = new \App\Entity\Users();
        $user->setUsername('testuser');
        $user->setPassword('testpassword');
        $user->setEmail('testuser@example.com');
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }


    /**
     * Test case to check if editing a user's profile is successful.
     */
    public function testEditUserProfile()
    {
        $client = static::createClient();
        $user = $this->createTestUser($client->getContainer()->get('doctrine.orm.entity_manager')); // Create a test user for editing

        $crawler = $client->request('GET', '/gestion_des_utilisateurs/modifier/' . $user->getUsername());

        $form = $crawler->selectButton('Modifier les informations')->form();
        // Modify the form data for editing
        $form['user_form[username]'] = 'editeduser';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/gestion_des_utilisateurs'));
        // Add assertions to check if the user's profile is successfully edited in the database
    }


    /**
     * Test case to check if deleting a user is successful.
     */
    public function testDeleteUser()
    {
        $client = static::createClient();
        $user = $this->createTestUser($client->getContainer()->get('doctrine.orm.entity_manager')); // Create a test user for deletion

        $client->request('GET', '/gestion_des_utilisateurs/supprimer/' . $user->getUsername());

        $this->assertTrue($client->getResponse()->isRedirect('/gestion_des_utilisateurs'));
        // Add assertions to check if the user is successfully deleted from the database
    }

}
