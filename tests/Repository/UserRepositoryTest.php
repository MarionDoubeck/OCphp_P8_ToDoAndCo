<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepositoryTest extends WebTestCase
{


    /**
     * Tests the constructor of the UserRepository class.
     */
    public function testConstructor()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($entityManager);
        // Create an instance of UserRepository using the mock ManagerRegistry object.
        $userRepository = new UserRepository($managerRegistry);
        // Check that the UserRepository is instantiated correctly.
        $this->assertInstanceOf(UserRepository::class, $userRepository);

    }//end testConstructor()


    /**
     * Tests the upgradePassword method of the UserRepository class.
     */
    public function testUpgradePassword()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userToEdit = $userRepository->findOneBy(['username' => 'UserToEdit']);

        // Call the upgradePassword method with the mock user object and a new hashed password
        $newHashedPassword = 'new_hashed_password';
        $userRepository = static::getContainer()->get(UserRepository::class);

        $userRepository->upgradePassword($userToEdit, $newHashedPassword);
        $this->assertSame($newHashedPassword, $userToEdit->getPassword(), 'The password has not been updated successfully.');

        $mockUser = $this->createMock(PasswordAuthenticatedUserInterface::class);
        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', get_class($mockUser)));
        $userRepository->upgradePassword($mockUser, 'new_hashed_password');

    }//end testUpgradePassword()


}
