<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    /** @var int $counter Counter for tracking iterations. */
    private $counter = 1;


    /**
     * UsersFixtures constructor.
     *
     * @param UserPasswordHasherInterface $passwordEncoder Password hasher for encoding user passwords.
     * @param SluggerInterface            $slugger         Slugger for generating slugs.
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
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
            $user = new Users();
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
        $dev = new Users();
        $dev->setUserName('marion');
        $dev->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $dev->setPassword(
            $this->passwordEncoder->hashPassword($dev, '123456')
        );
        $dev->setEmail('mariondoubeck@yahoo.fr');
        $manager->persist($dev);
        $this->addReference('user_'.$this->counter, $dev);
        $this->counter++;

        //User_11 is 'Anonymus'.
        $anonymus = new Users();
        $anonymus->setUserName('Anonymus');
        $anonymus->setRoles(['ROLE_USER']);
        $anonymus->setPassword(
            $this->passwordEncoder->hashPassword($anonymus, 'secret')
        );
        $anonymus->setEmail('fake@fake.fk');
        $manager->persist($anonymus);
        $this->addReference('user_'.$this->counter, $anonymus);
        $this->counter++;

        $manager->flush();

    }//end load()

}
