<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
	private User $user;

	protected function setUp(): void
	{
		$this->user = new User();
	}

	public function testConstructor(): void
	{
		$user = new User();

		$this->assertInstanceOf(ArrayCollection::class, $user->getTask());
		$this->assertEmpty($user->getTask());
	}

	public function testGetSetId(): void
	{
		$this->assertNull($this->user->getId());
	}

	public function testGetSetEmail(): void
	{
		$email = 'test@example.com';

		$this->assertNull($this->user->getEmail());

		$result = $this->user->setEmail($email);

		$this->assertSame($this->user, $result);
		$this->assertSame($email, $this->user->getEmail());
	}

	public function testGetUserIdentifier(): void
	{
		$email = 'test@example.com';
		$this->user->setEmail($email);

		$this->assertSame($email, $this->user->getUserIdentifier());
	}

	public function testGetUserIdentifierWithNullEmail(): void
	{
		$this->assertSame('', $this->user->getUserIdentifier());
	}

	public function testGetSetRoles(): void
	{
		$roles = ['ROLE_ADMIN', 'ROLE_MANAGER'];

		$defaultRoles = $this->user->getRoles();
		$this->assertContains('ROLE_USER', $defaultRoles);
		$this->assertCount(1, $defaultRoles);

		$result = $this->user->setRoles($roles);

		$this->assertSame($this->user, $result);

		$userRoles = $this->user->getRoles();
		$this->assertContains('ROLE_USER', $userRoles);
		$this->assertContains('ROLE_ADMIN', $userRoles);
		$this->assertContains('ROLE_MANAGER', $userRoles);
		$this->assertCount(3, $userRoles);
	}

	public function testGetRolesRemovesDuplicates(): void
	{
		$roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_ADMIN'];
		$this->user->setRoles($roles);

		$userRoles = $this->user->getRoles();
		$this->assertCount(2, $userRoles);
		$this->assertContains('ROLE_USER', $userRoles);
		$this->assertContains('ROLE_ADMIN', $userRoles);
	}

	public function testGetSetPassword(): void
	{
		$password = 'hashedPassword123';

		$this->assertNull($this->user->getPassword());

		$result = $this->user->setPassword($password);

		$this->assertSame($this->user, $result);
		$this->assertSame($password, $this->user->getPassword());
	}

	public function testGetSetUsername(): void
	{
		$username = 'testuser';

		$this->assertNull($this->user->getUsername());

		$result = $this->user->setUsername($username);

		$this->assertSame($this->user, $result);
		$this->assertSame($username, $this->user->getUsername());
	}

	public function testGetTask(): void
	{
		$tasks = $this->user->getTask();

		$this->assertInstanceOf(ArrayCollection::class, $tasks);
		$this->assertEmpty($tasks);
	}

	public function testAddTask(): void
	{
		$task = $this->createMock(Task::class);
		$task->expects($this->once())
			->method('setUserEntity')
			->with($this->user);

		$result = $this->user->addTask($task);

		$this->assertSame($this->user, $result);
		$this->assertTrue($this->user->getTask()->contains($task));
		$this->assertCount(1, $this->user->getTask());
	}

	public function testAddTaskDoesNotAddDuplicate(): void
	{
		$task = $this->createMock(Task::class);
		$task->expects($this->once())
			->method('setUserEntity')
			->with($this->user);

		$this->user->addTask($task);
		$this->user->addTask($task); // Add same task again

		$this->assertCount(1, $this->user->getTask());
	}

	public function testRemoveTask(): void
	{
		$task = $this->createMock(Task::class);

		// First add the task
		$this->user->addTask($task);
		$this->assertTrue($this->user->getTask()->contains($task));

		// Mock the task to return this user as its user entity
		$task->expects($this->once())
			->method('getUserEntity')
			->willReturn($this->user);
		$task->expects($this->once())
			->method('setUserEntity')
			->with(null);

		$result = $this->user->removeTask($task);

		$this->assertSame($this->user, $result);
		$this->assertFalse($this->user->getTask()->contains($task));
		$this->assertEmpty($this->user->getTask());
	}

	public function testRemoveTaskWhenTaskHasDifferentUser(): void
	{
		$task = $this->createMock(Task::class);
		$otherUser = new User();

		$this->user->addTask($task);

		// Mock the task to return a different user
		$task->expects($this->once())
			->method('getUserEntity')
			->willReturn($otherUser);
		$task->expects($this->never())
			->method('setUserEntity');

		$this->user->removeTask($task);

		$this->assertFalse($this->user->getTask()->contains($task));
	}

	public function testRemoveNonExistentTask(): void
	{
		$task = $this->createMock(Task::class);
		$task->expects($this->never())
			->method('getUserEntity');

		$result = $this->user->removeTask($task);

		$this->assertSame($this->user, $result);
		$this->assertEmpty($this->user->getTask());
	}

	public function testFluentInterface(): void
	{
		$user = new User()
			->setEmail('test@example.com')
			->setUsername('testuser')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN']);

		$this->assertSame('test@example.com', $user->getEmail());
		$this->assertSame('testuser', $user->getUsername());
		$this->assertSame('password', $user->getPassword());
		$this->assertContains('ROLE_ADMIN', $user->getRoles());
	}
}
