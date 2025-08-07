<?php

namespace App\Tests\Unit;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
	private Task $task;

	protected function setUp(): void
	{
		$this->task = new Task();
	}

	public function testGetSetId(): void
	{
		$this->assertNull($this->task->getId());
	}

	public function testGetSetCreatedAt(): void
	{
		$createdAt = new \DateTimeImmutable('2023-01-01 10:00:00');

		// The constructor automatically sets createdAt, so it should not be null
		$this->assertInstanceOf(\DateTimeImmutable::class, $this->task->getCreatedAt());

		$result = $this->task->setCreatedAt($createdAt);

		$this->assertSame($this->task, $result);
		$this->assertSame($createdAt, $this->task->getCreatedAt());
	}

	public function testGetSetTitle(): void
	{
		$title = 'Test Task Title';

		$this->assertNull($this->task->getTitle());

		$result = $this->task->setTitle($title);

		$this->assertSame($this->task, $result);
		$this->assertSame($title, $this->task->getTitle());
	}

	public function testGetSetContent(): void
	{
		$content = 'This is the task content description';

		$this->assertNull($this->task->getContent());

		$result = $this->task->setContent($content);

		$this->assertSame($this->task, $result);
		$this->assertSame($content, $this->task->getContent());
	}

	public function testIsDoneGetSet(): void
	{
		$this->assertNull($this->task->isDone());

		$result = $this->task->setIsDone(true);

		$this->assertSame($this->task, $result);
		$this->assertTrue($this->task->isDone());

		$this->task->setIsDone(false);
		$this->assertFalse($this->task->isDone());

		$this->task->setIsDone(null);
		$this->assertNull($this->task->isDone());
	}

	public function testToggle(): void
	{
		$result = $this->task->toggle(true);

		$this->assertSame($this->task, $result);
		$this->assertTrue($this->task->isDone());

		$this->task->toggle(false);
		$this->assertFalse($this->task->isDone());
	}

	public function testToggleOverridesExistingValue(): void
	{
		$this->task->setIsDone(true);
		$this->assertTrue($this->task->isDone());

		$this->task->toggle(false);
		$this->assertFalse($this->task->isDone());

		$this->task->toggle(true);
		$this->assertTrue($this->task->isDone());
	}

	public function testGetSetUserEntity(): void
	{
		$user = $this->createMock(User::class);

		$this->assertNull($this->task->getUserEntity());

		$result = $this->task->setUserEntity($user);

		$this->assertSame($this->task, $result);
		$this->assertSame($user, $this->task->getUserEntity());
	}

	public function testSetUserEntityWithNull(): void
	{
		$user = $this->createMock(User::class);
		$this->task->setUserEntity($user);
		$this->assertSame($user, $this->task->getUserEntity());

		$this->task->setUserEntity(null);
		$this->assertNull($this->task->getUserEntity());
	}

	public function testFluentInterface(): void
	{
		$user = $this->createMock(User::class);
		$createdAt = new \DateTimeImmutable('2023-01-01 10:00:00');

		$task = (new Task())
			->setCreatedAt($createdAt)
			->setTitle('Test Task')
			->setContent('Test Content')
			->setIsDone(true)
			->setUserEntity($user);

		$this->assertSame($createdAt, $task->getCreatedAt());
		$this->assertSame('Test Task', $task->getTitle());
		$this->assertSame('Test Content', $task->getContent());
		$this->assertTrue($task->isDone());
		$this->assertSame($user, $task->getUserEntity());
	}

	public function testFluentInterfaceWithToggle(): void
	{
		$task = (new Task())
			->setTitle('Test Task')
			->setIsDone(false)
			->toggle(true);

		$this->assertSame('Test Task', $task->getTitle());
		$this->assertTrue($task->isDone());
	}

	public function testCompleteWorkflow(): void
	{
		$user = $this->createMock(User::class);
		$createdAt = new \DateTimeImmutable();

		// Create a new task
		$this->task
			->setCreatedAt($createdAt)
			->setTitle('Complete a project')
			->setContent('Finish the entire project by the deadline')
			->setUserEntity($user)
			->setIsDone(false);

		// Verify initial state
		$this->assertSame($createdAt, $this->task->getCreatedAt());
		$this->assertSame('Complete a project', $this->task->getTitle());
		$this->assertSame('Finish the entire project by the deadline', $this->task->getContent());
		$this->assertSame($user, $this->task->getUserEntity());
		$this->assertFalse($this->task->isDone());

		// Mark as done
		$this->task->toggle(true);
		$this->assertTrue($this->task->isDone());

		// Mark as not done again
		$this->task->toggle(false);
		$this->assertFalse($this->task->isDone());
	}

	public function testDateTimeImmutableConstraint(): void
	{
		$createdAt = new \DateTimeImmutable('2023-12-25 15:30:45');
		$this->task->setCreatedAt($createdAt);

		$retrievedDate = $this->task->getCreatedAt();
		$this->assertInstanceOf(\DateTimeImmutable::class, $retrievedDate);
		$this->assertSame($createdAt, $retrievedDate);

		// Verify immutability
		$this->assertSame($createdAt->format('Y-m-d H:i:s'), $retrievedDate->format('Y-m-d H:i:s'));
	}
}
