<?php

namespace App\Tests\Repository;

use PHPUnit\Framework\TestCase;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepositoryTest extends TestCase
{
    public function testConstructor()
    {
        // Create a mock object for ManagerRegistry.
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        // Create an instance of UserRepository using the mock ManagerRegistry object.
        $taskRepository = new TaskRepository($managerRegistry);

        // Check that the UserRepository is instantiated correctly.
        $this->assertInstanceOf(TaskRepository::class, $taskRepository);
    }
}
