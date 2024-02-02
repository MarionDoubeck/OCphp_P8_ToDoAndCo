<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Tasks;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;

class TasksTest extends TestCase
{
    public function testConstructor()
    {
        $task = new Tasks();
        $this->assertInstanceOf(Tasks::class, $task);
    }

    public function testGetId()
    {
        $task = new Tasks();
        $this->assertNull($task->getId());
    }

    public function testTitle()
    {
        $task = new Tasks();
        $task->setTitle('Task Title');
        $this->assertEquals('Task Title', $task->getTitle());
    }

    public function testContent()
    {
        $task = new Tasks();
        $task->setContent('Task Content');
        $this->assertEquals('Task Content', $task->getContent());
    }

    public function testCreatedAt()
    {
        $task = new Tasks();
        $now = new \DateTimeImmutable();
        $task->setCreatedAt($now);
        $this->assertEquals($now, $task->getCreatedAt());
    }

    public function testIsDone()
    {
        $task = new Tasks();
        $task->setIsDone(true);
        $this->assertTrue($task->isDone());
    }

    public function testToggle()
    {
        $task = new Tasks();
        $task->toggle(!$task->isDone());
        $this->assertTrue($task->isDone());
    }

    public function testAuthor()
    {
        $task = new Tasks();
        $user = new Users();
        $task->setAuthor($user);
        $this->assertInstanceOf(Users::class, $task->getAuthor());
    }


    //Tests pour les comportements de bord

    public function testSetTitleWithEmptyString()
    {
        $task = new Tasks();
        $task->setTitle('');
        $this->assertEquals('', $task->getTitle());
    }

    public function testSetContentWithEmptyString()
    {
        $task = new Tasks();
        $task->setContent('');
        $this->assertEquals('', $task->getContent());
    }

    public function testSetCreatedAtWithFutureDate()
    {
        $task = new Tasks();
        $futureDate = new \DateTimeImmutable('tomorrow');
        $task->setCreatedAt($futureDate);
        $this->assertEquals($futureDate, $task->getCreatedAt());
    }

    public function testSetIsDoneWithBooleanValues()
    {
        $task = new Tasks();
        $task->setIsDone(true);
        $this->assertTrue($task->isDone());

        $task->setIsDone(false);
        $this->assertFalse($task->isDone());
    }

    public function testToggleOnCompletedTask()
    {
        $task = new Tasks();
        $task->setIsDone(true);
        $task->toggle(!$task->isDone());
        $this->assertFalse($task->isDone());
    }

    public function testSetAuthorWithNullInstance()
    {
        $task = new Tasks();
        $task->setAuthor(null);
        $this->assertNull($task->getAuthor());
    }

    public function testSetAuthorWithValidUserInstance()
    {
        $task = new Tasks();
        $user = new Users();
        $task->setAuthor($user);
        $this->assertSame($user, $task->getAuthor());
    }

    public function testGetAuthorWithoutSettingAuthor()
    {
        $task = new Tasks();
        $this->assertNull($task->getAuthor());
    }

    public function testToggleWithoutSettingIsDone()
    {
        $task = new Tasks();
        $task->toggle(!$task->isDone());
        $this->assertTrue($task->isDone());
    }

    // Test supplémentaire si vous utilisez une base de données
    public function testGetIdAfterDatabasePersistence(EntityManagerInterface $entityManager)
     {
         //$entityManager = // récupérez votre gestionnaire d'entités ici
         $task = new Tasks();
         $entityManager->persist($task);
         $entityManager->flush();
         $this->assertNotNull($task->getId());
    }
}
