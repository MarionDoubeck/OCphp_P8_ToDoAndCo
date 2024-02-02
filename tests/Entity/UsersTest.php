<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Users;
use App\Entity\Tasks;

class UsersTest extends TestCase
{
    public function testConstructor()
    {
        $user = new Users();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertInstanceOf(\Doctrine\Common\Collections\ArrayCollection::class, $user->getTasks());
    }

    public function testGetId()
    {
        $user = new Users();
        $this->assertNull($user->getId());
    }

    public function testUsername()
    {
        $user = new Users();
        $user->setUsername('john_doe');
        $this->assertEquals('john_doe', $user->getUsername());
    }

    // Teste la méthode getUserIdentifier
    public function testgetUserIdentifier()
    {
        $user = new Users();
        $user->setUsername('john_doe');
        $this->assertEquals('john_doe', $user->getUserIdentifier());
    }

    // Teste les rôles
    public function testRoles()
    {
        $user = new Users();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    // Teste le mot de passe
    public function testPassword()
    {
        $user = new Users();
        $user->setPassword('hashed_password');
        $this->assertEquals('hashed_password', $user->getPassword());
    }

    // Teste la suppression des identifiants
    public function testEraseCredentials()
    {
        $user = new Users();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    // Teste l'email
    public function testEmail()
    {
        $user = new Users();
        $user->setEmail('john@example.com');
        $this->assertEquals('john@example.com', $user->getEmail());
    }

    // Teste la gestion des tâches
    public function testTasks()
    {
        $user = new Users();
        $task = new Tasks();

        $user->addTask($task);
        $this->assertTrue($user->getTasks()->contains($task));

        $user->removeTask($task);
        $this->assertFalse($user->getTasks()->contains($task));
    }

    // Teste le getter pour la collection de tâches
    public function testGetTasks()
    {
        $user = new Users();
        $this->assertInstanceOf(\Doctrine\Common\Collections\ArrayCollection::class, $user->getTasks());
    }


    //Tests pour les comportements de bord
    // Teste la gestion des rôles avec des rôles dupliqués
    public function testSetRolesWithDuplicateRoles()
    {
        $user = new Users();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    // Teste addTask avec une tâche existante
    public function testAddTaskWithExistingTask()
    {
        $user = new Users();
        $task = new Tasks();
        $user->addTask($task);
        $user->addTask($task);
        $this->assertCount(1, $user->getTasks());
    }

    // Teste removeTask avec une tâche inexistante
    public function testRemoveTaskWithNonExistingTask()
    {
        $user = new Users();
        $task = new Tasks();
        $user->removeTask($task);
        $this->assertCount(0, $user->getTasks());
    }

    // Teste setPassword avec un mot de passe vide
    public function testSetPasswordWithEmptyPassword()
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new Users();
        $user->setPassword('');
    }

    // Teste setEmail avec un e-mail invalide
    public function testSetEmailWithInvalidEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new Users();
        $user->setEmail('not_an_email');
    }

    // Teste setUsername avec un nom d'utilisateur vide
    public function testSetUsernameWithEmptyUsername()
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = new Users();
        $user->setUsername('');
    }

    // Teste les comportements de bord (exemple: setRoles avec un tableau vide)
    public function testSetRolesWithEmptyArray()
    {
        $user = new Users();
        $user->setRoles([]);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }
}