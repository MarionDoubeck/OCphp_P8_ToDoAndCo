<?php

namespace App\Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class TestCase extends WebTestCase
{
    protected $entityManager;
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        $platform = $connection->getDatabasePlatform();

        //empty database
        $connection->executeStatement($platform->getTruncateTableSQL('users', true));
        $connection->executeStatement($platform->getTruncateTableSQL('tasks', true));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');

        //refill database
        $user1 = new \App\Entity\Users();
        $user1->setUsername('admin');
        $user1->setPassword('admin');
        $user1->setEmail('admin@example.com');
        $user1->setRoles(['ROLE_USER, ROLE_ADMIN']);
        
        $user2 = new \App\Entity\Users();
        $user2->setUsername('Anonymus');
        $user2->setPassword('anonymus');
        $user2->setEmail('anonymus@example.com');
        $user2->setRoles(['ROLE_USER']);
        
        $user3 = new \App\Entity\Users();
        $user3->setUsername('userToLog');
        $user3->setPassword('userToLog');
        $user3->setEmail('userToLog@example.com');
        $user3->setRoles(['ROLE_USER']);
        
        $user4 = new \App\Entity\Users();
        $user4->setUsername('userToEdit');
        $user4->setPassword('userToEdit');
        $user4->setEmail('userToEdit@example.com');
        $user4->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->persist($user3);
        $this->entityManager->persist($user4);
        $this->entityManager->flush();

    }

    public function getClient()
    {
        return $this->client;
    }

    /* public function tearDown(): void
    {
        parent::tearDown();
    } */

}
