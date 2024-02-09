<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{


    /**
     * Load dummy task data into the database.
     *
     * This function generates fake task data to simulate the addition of task.
     * Task titles, contents, authors, creationDate, and isDone are randomized.
     *
     * @param ObjectManager $manager The entity manager to persist the data.
     * 
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($tsk = 1 ; $tsk <= 25 ; $tsk++) {
            $task = new Task();
            $task->setTitle($faker->text(rand(5,25)));
            $task->setContent($faker->text(rand(30,150)));
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setIsDone($faker->boolean);
            //Half tasks related to 'Anonymus' who is user_11.
            if(rand(0,1) >= 1){
                $user = $this->getReference('user_11');
            } else{
                $user = $this->getReference('user_'.rand(1,10));
            }
            $task->setAuthor($user);

            $manager->persist($task);
        }
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
        return ['groupApp'];
    }
}
