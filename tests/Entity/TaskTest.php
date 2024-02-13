<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{


    public function testTaskEntity()
    {
        $task = new Task;
        $task->setTitle('Title test');
        $task->setContent('Content test');
        $dateNow = new DateTimeImmutable;
        $task->setCreatedAt($dateNow);
        $task->setIsDone(true);
        $author = new User;
        $task->setAuthor($author);

        $this->assertEquals('Title test', $task->getTitle());
        $this->assertEquals('Content test', $task->getContent());
        $this->assertEquals($dateNow, $task->getCreatedAt());
        $this->assertEquals(true, $task->isIsDone());
        $this->assertEquals($author, $task->getAuthor());
        $task->toggle(false);
        $this->assertEquals(false, $task->isIsDone());
        $this->assertNull($task->getId());
    }


}