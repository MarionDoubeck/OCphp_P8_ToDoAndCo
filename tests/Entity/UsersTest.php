<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Users;
use App\Entity\Tasks;

/**
 * Class UsersTest
 * @package App\tests\Entity
 * @coversDefaultClass \App\Entity\Users
 */
class UsersTest extends TestCase
{
    /**
     * Test the constructor of Users class.
     *
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $user = new Users();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertInstanceOf(\Doctrine\Common\Collections\ArrayCollection::class, $user->getTasks());
    }

    /**
     * Test the getId method of Users class.
     *
     * @covers ::getId
     */
    public function testGetId()
    {
        $user = new Users();
        $this->assertNull($user->getId());
    }

    /**
     * Test the setUsername and getUsername methods of Users class.
     *
     * @covers ::setUsername
     * @covers ::getUsername
     */
    public function testUsername()
    {
        $user = new Users();
        $user->setUsername('john_doe');
        $this->assertEquals('john_doe', $user->getUsername());
    }

    /**
     * Test the getUserIdentifier method of Users class.
     *
     * @covers ::getUserIdentifier
     */
    public function testgetUserIdentifier()
    {
        $user = new Users();
        $user->setUsername('john_doe');
        $this->assertEquals('john_doe', $user->getUserIdentifier());
    }


    // Test roles
 /*   public function testRoles()
    {
        $user = new Users();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }
*/
    /**
     * Test the setPassword and getPassword methods of Users class.
     *
     * @covers ::setPassword
     * @covers ::getPassword
     */
    public function testPassword()
    {
        $user = new Users();
        $user->setPassword('hashed_password');
        $this->assertEquals('hashed_password', $user->getPassword());
    }

    /**
     * Test the eraseCredentials method of Users class.
     *
     * @covers ::eraseCredentials
     */
    public function testEraseCredentials()
    {
        $user = new Users();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    /**
     * Test the setEmail and getEmail methods of Users class.
     *
     * @covers ::setEmail
     * @covers ::getEmail
     */
    public function testEmail()
    {
        $user = new Users();
        $user->setEmail('john@example.com');
        $this->assertEquals('john@example.com', $user->getEmail());
    }

    /**
     * Test the addTask and removeTask methods of Users class.
     *
     * @covers ::addTask
     * @covers ::removeTask
     */
    public function testTasks()
    {
        $user = new Users();
        $task = new Tasks();

        $user->addTask($task);
        $this->assertTrue($user->getTasks()->contains($task));

        $user->removeTask($task);
        $this->assertFalse($user->getTasks()->contains($task));
    }

    /**
     * Test the getTasks method of Users class.
     *
     * @covers ::getTasks
     */
    public function testGetTasks()
    {
        $user = new Users();
        $this->assertInstanceOf(\Doctrine\Common\Collections\ArrayCollection::class, $user->getTasks());
    }

    /**
     * Test behavior with duplicate roles.
     *
     * @covers ::setRoles
     */
    public function testSetRolesWithDuplicateRoles()
    {
        $user = new Users();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

   
}
