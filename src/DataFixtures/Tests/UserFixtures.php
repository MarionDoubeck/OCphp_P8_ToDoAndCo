<?php

namespace App\DataFixtures\Tests;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{


    /**
     * Load dummy user data into the database for tests
     *
     * @param ObjectManager $manager The entity manager to persist the data.
     * 
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUserName('admin');
        $user1->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user1->setPassword('admin');
        $user1->setEmail('admin@admin.fk');
        $manager->persist($user1);
        $this->addReference('admin', $user1);

        $user3 = new User();
        $user3->setUserName('userToLog');
        $user3->setRoles(['ROLE_USER']);
        $user3->setPassword('user');
        $user3->setEmail('fakeusertolog@fake.fk');
        $manager->persist($user3);
        $this->addReference('user_to_log', $user3);

        $user4 = new User();
        $user4->setUserName('userToEdit');
        $user4->setRoles(['ROLE_USER']);
        $user4->setPassword('user');
        $user4->setEmail('fakeusertoedit@fake.fk');
        $manager->persist($user4);

        $user5 = new User();
        $user5->setUserName('userToDelete');
        $user5->setRoles(['ROLE_USER']);
        $user5->setPassword('user');
        $user5->setEmail('fakeusertodelete@fake.fk');
        $manager->persist($user5);

        $user6 = new User();
        $user6->setUserName('authorOfTasks');
        $user6->setRoles(['ROLE_USER']);
        $user6->setPassword('user');
        $user6->setEmail('fakeAuthor@fake.fk');
        $manager->persist($user6);
        $this->addReference('author_of_tasks', $user6);

        $manager->flush();

    }//end load()


    /**
     * Returns the groups associated with this entity.
     *
     * @return array The groups associated with this entity.
     */
    public static function getGroups(): array
    {
        return ['groupTest'];

    }//end getGroups()


}
