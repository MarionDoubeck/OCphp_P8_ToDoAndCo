<?php

namespace App\DataFixtures\Tests;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{


    /**
     * Load dummy task data into the database for tests.
     *
     * @param ObjectManager $manager The entity manager to persist the data.
     * 
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $task1 = new Task();
        $task1->setTitle('taskToDisplay');
        $task1->setContent('taskToDisplay');
        $task1->setCreatedAt(new \DateTimeImmutable());
        $task1->setIsDone(false);
        $task1->setAuthor($this->getReference('user_to_log'));
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setTitle('taskToEdit');
        $task2->setContent('taskToEdit');
        $task2->setCreatedAt(new \DateTimeImmutable());
        $task2->setIsDone(false);
        $task2->setAuthor($this->getReference('user_to_log'));
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setTitle('taskToDelete');
        $task3->setContent('taskToDelete');
        $task3->setCreatedAt(new \DateTimeImmutable());
        $task3->setIsDone(false);
        $task3->setAuthor($this->getReference('user_to_log'));
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setTitle('taskAnonymusToDelete');
        $task4->setContent('taskAnonymusToDelete');
        $task4->setCreatedAt(new \DateTimeImmutable());
        $task4->setIsDone(false);
        $task4->setAuthor($this->getReference('anonymus'));
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setTitle('taskToToggle');
        $task5->setContent('taskToToggle');
        $task5->setCreatedAt(new \DateTimeImmutable());
        $task5->setIsDone(false);
        $task5->setAuthor($this->getReference('user_to_log'));
        $manager->persist($task2);

        $manager->flush();

    }//end load()


    /**
     * Get the dependencies of this fixture.
     *
     * This function specifies the dependencies of this fixture on other fixtures.
     *
     * @return array An array of dependent fixture classes.
     */
    public function getDependencies():array
    {
        return [
            UserFixtures::class
        ];
    }//end getDependencies()


    public static function getGroups(): array
    {
        return ['groupTest'];
    }
}
