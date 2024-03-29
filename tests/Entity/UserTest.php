<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{


    /**
     * Test the User entity.
     *
     * @return void
     */
    public function testUserEntity(): void
    {
        $user = new User;
        $user->setUsername('lili');
        $user->setPassword('lilipassword');
        $user->setEmail('lili@test.com');
        $user->setRoles(['ROLE_USER']);

        $this->assertEquals('lili', $user->getUserIdentifier());
        $this->assertEquals('lili', $user->getUsername());
        $this->assertEquals('lilipassword', $user->getPassword());
        $this->assertEquals('lili@test.com', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertNull($user->getId());
        $this->assertNull($user->eraseCredentials());

    }//end testUserEntity()


}//end class
