<?php

namespace App\Tests\Integration;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskControllerIntegrationTest extends TestCase
{
    public function testCreateWithNonUserInstance(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $request = $this->createMock(Request::class);
        $form = $this->createMock(FormInterface::class);
        $nonUser = $this->createMock(UserInterface::class); // Not a User entity

        // Mock form behavior for submitted and valid form
        $form->expects($this->once())->method('handleRequest')->with($request);
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        // Create controller mock
        $controller = $this->getMockBuilder(TaskController::class)
            ->onlyMethods(['createForm', 'getUser', 'addFlash', 'redirectToRoute'])
            ->getMock();

        $controller->expects($this->once())
            ->method('createForm')
            ->with($this->equalTo(TaskType::class), $this->isInstanceOf(Task::class))
            ->willReturn($form);

        $controller->expects($this->once())
            ->method('getUser')
            ->willReturn($nonUser); // Return non-User instance

        $controller->expects($this->once())
            ->method('addFlash')
            ->with('error', 'Vous devez être connecté pour créer une tâche.');

        $controller->expects($this->once())
            ->method('redirectToRoute')
            ->with('task_list');

        // Call the method
        $controller->create($request, $entityManager);
    }

    public function testEditWithNonUserInstance(): void
    {
        $user = new User();
        $task = new Task();
        $task->setUserEntity($user); // Task belongs to a user
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $request = $this->createMock(Request::class);
        $form = $this->createMock(FormInterface::class);
        $nonUser = $this->createMock(UserInterface::class); // Not a User entity

        // Mock form behavior for submitted and valid form
        $form->expects($this->once())->method('handleRequest')->with($request);
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        // Create controller mock
        $controller = $this->getMockBuilder(TaskController::class)
            ->onlyMethods(['createForm', 'getUser', 'addFlash', 'redirectToRoute'])
            ->getMock();

        $controller->expects($this->once())
            ->method('createForm')
            ->with(TaskType::class, $task)
            ->willReturn($form);

        // First call for ownership check returns the owner, second call returns non-User
        $controller->expects($this->exactly(2))
            ->method('getUser')
            ->willReturnOnConsecutiveCalls($user, $nonUser);

        $controller->expects($this->once())
            ->method('addFlash')
            ->with('error', 'Vous devez être connecté pour créer une tâche.');

        $controller->expects($this->once())
            ->method('redirectToRoute')
            ->with('task_list');

        // Call the method
        $controller->edit($task, $request, $entityManager);
    }

    public function testDeleteTaskWithNoUserAssociated(): void
    {
        $task = new Task(); // Task without user - this is the scenario we want to test
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $controller = $this->getMockBuilder(TaskController::class)
            ->onlyMethods(['addFlash', 'redirectToRoute'])
            ->getMock();

        $controller->expects($this->once())
            ->method('addFlash')
            ->with('error', 'Vous ne pouvez pas supprimer une tâche qui n\'a pas d\'utilisateur associé.');

        $controller->expects($this->once())
            ->method('redirectToRoute')
            ->with('task_list');

        // Call the method - this should trigger the first error condition
        $controller->deleteTask($task, $entityManager);
    }
}
