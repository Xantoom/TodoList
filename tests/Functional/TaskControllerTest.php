<?php

namespace App\Tests\Functional;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TaskControllerTest extends WebTestCase
{
	use Factories, ResetDatabase;

	public function testListAction(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/tasks');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('task_list');
	}

	public function testDoneListAction(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/tasks-done');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('task_done_list');
	}

	public function testListWithTasks(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		TaskFactory::createOne([
			'title' => 'Test Task',
			'content' => 'Test Content',
			'isDone' => false,
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks');

		self::assertResponseIsSuccessful();
	}

	public function testListWithCompletedTasks(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		TaskFactory::createOne([
			'title' => 'Completed Task',
			'isDone' => true,
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks');

		self::assertResponseIsSuccessful();
	}

	public function testDoneListWithCompletedTasks(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		TaskFactory::createOne([
			'title' => 'Completed Task',
			'isDone' => true,
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks-done');

		self::assertResponseIsSuccessful();
		self::assertSelectorTextContains('h4 a', 'Completed Task');
	}

	public function testCreateActionGet(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/tasks/create');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('task_create');
		self::assertSelectorExists('form');
		self::assertSelectorExists('button[type="submit"]');
	}

	public function testCreateActionPost(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$crawler = $client->request('GET', '/tasks/create');

		$form = $crawler->selectButton('Ajouter')->form();
		$client->submit($form, [
			'task[title]' => 'New Task',
			'task[content]' => 'New Task Content'
		]);

		self::assertResponseRedirects('/tasks');
	}

	public function testEditActionGet(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'title' => 'Edit Task',
			'content' => 'Edit Content',
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks/' . $task->getId() . '/edit');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('task_edit');
		self::assertSelectorExists('form');
		self::assertSelectorExists('button[type="submit"]');
	}

	public function testEditActionPost(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'title' => 'Original Title',
			'content' => 'Original Content',
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$crawler = $client->request('GET', '/tasks/' . $task->getId() . '/edit');

		$form = $crawler->selectButton('Modifier')->form();
		$client->submit($form, [
			'task[title]' => 'Updated Title',
			'task[content]' => 'Updated Content'
		]);

		self::assertResponseRedirects('/tasks');
	}

	public function testToggleTaskToDone(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'isDone' => false,
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks/' . $task->getId() . '/toggle');

		self::assertResponseRedirects('/tasks-done');
	}

	public function testToggleTaskToUndone(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'isDone' => true,
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks/' . $task->getId() . '/toggle');

		self::assertResponseRedirects('/tasks');
	}

	public function testDeleteTaskAsOwner(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'userEntity' => $user
		]);

		$client->loginUser($user);
		$client->request('GET', '/tasks/' . $task->getId() . '/delete');

		self::assertResponseRedirects('/tasks');
	}

	public function testCannotDeleteOtherUserTask(): void
	{
		$client = static::createClient();

		$owner = UserFactory::createOne()->_real();
		$otherUser = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'userEntity' => $owner
		]);

		$client->loginUser($otherUser);
		$client->request('GET', '/tasks/' . $task->getId() . '/delete');

		self::assertResponseStatusCodeSame(403);
	}

	public function testCannotEditOtherUserTask(): void
	{
		$client = static::createClient();

		$owner = UserFactory::createOne()->_real();
		$otherUser = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'userEntity' => $owner
		]);

		$client->loginUser($otherUser);
		$client->request('GET', '/tasks/' . $task->getId() . '/edit');

		self::assertResponseStatusCodeSame(403);
	}

	public function testCannotToggleOtherUserTask(): void
	{
		$client = static::createClient();

		$owner = UserFactory::createOne()->_real();
		$otherUser = UserFactory::createOne()->_real();
		$task = TaskFactory::createOne([
			'userEntity' => $owner
		]);

		$client->loginUser($otherUser);
		$client->request('GET', '/tasks/' . $task->getId() . '/toggle');

		self::assertResponseStatusCodeSame(403);
	}

	public function testTaskNotFound(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/tasks/999/edit');

		self::assertResponseStatusCodeSame(404);
	}

	public function testListEmptyTasks(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/tasks');

		self::assertResponseIsSuccessful();
	}

	public function testDeleteButtonOnlyForOwner(): void
	{
		$client = static::createClient();

		$owner = UserFactory::createOne()->_real();
		$otherUser = UserFactory::createOne()->_real();

		TaskFactory::createOne([
			'title' => 'Owner Task',
			'userEntity' => $owner
		]);

		TaskFactory::createOne([
			'title' => 'Other Task',
			'userEntity' => $otherUser
		]);

		$client->loginUser($owner);
		$crawler = $client->request('GET', '/tasks');

		$deleteButtons = $crawler->filter('form[action*="delete"] button');
		self::assertCount(1, $deleteButtons);
	}
}
