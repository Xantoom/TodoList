<?php

namespace App\Tests\Unit;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends TestCase
{
    public function testDeleteTaskWithNoUserEntity(): void
    {
        $task = new Task(); // Task without user entity
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Create a partial mock of TaskController to test the specific scenario
        $controller = $this->getMockBuilder(TaskController::class)
            ->onlyMethods(['addFlash', 'redirectToRoute'])
            ->getMock();

        // Expect the error flash message
        $controller->expects($this->once())
            ->method('addFlash')
            ->with('error', 'Vous ne pouvez pas supprimer une tâche qui n\'a pas d\'utilisateur associé.');

        // Expect redirect to task list
        $controller->expects($this->once())
            ->method('redirectToRoute')
            ->with('task_list');

        // Call the method - this should trigger the first error condition
        $controller->deleteTask($task, $entityManager);
    }

    public function testTaskEntityAutomaticallySetCreatedAt(): void
    {
        $task = new Task();

        // Verify that createdAt is automatically set by constructor
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
        $this->assertLessThanOrEqual(time(), $task->getCreatedAt()->getTimestamp());
    }
}
