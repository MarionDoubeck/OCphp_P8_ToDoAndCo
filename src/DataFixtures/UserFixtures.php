<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /** @var int $counter Counter for tracking iterations. */
    private $counter = 1;


    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordHasherInterface $passwordEncoder Password hasher for encoding user passwords.
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    ) {
    }//end __construct()


    /**
     * Load dummy user data into the database.
     *
     * This function generates fake user data to simulate user registration.
     * User email, password, username, are randomized.
     *
     * @param ObjectManager $manager The entity manager to persist the data.
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1 ; $usr <= 9 ; $usr++) {
            $user = new User();
            $user->setUserName($faker->userName);
            $roles = $faker->randomElement([
                ['ROLE_USER'],
                ['ROLE_ADMIN', 'ROLE_USER'],
            ]);
            $user->setRoles($roles);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $user->setEmail($faker->email);


            $manager->persist($user);
            $this->addReference('user_'.$this->counter, $user);
            $this->counter++;
        }
        //User_10 is me.
        $dev = new User();
        $dev->setUserName('marion');
        $dev->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $dev->setPassword(
            $this->passwordEncoder->hashPassword($dev, '123456')
        );
        $dev->setEmail('mariondoubeck@yahoo.fr');
        $manager->persist($dev);
        $this->addReference('user_'.$this->counter, $dev);
        $this->counter++;

        $manager->flush();

    }//end load()


    public static function getGroups(): array
    {
        return ['groupApp'];
    }
}
