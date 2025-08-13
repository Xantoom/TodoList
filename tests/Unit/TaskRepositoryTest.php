<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registry = $this->createMock(ManagerRegistry::class);

        $this->registry
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);

        $this->taskRepository = new TaskRepository($this->registry);
    }

    public function testConstructor(): void
    {
        // Test that the repository is properly instantiated
        $this->assertInstanceOf(TaskRepository::class, $this->taskRepository);
    }
}
