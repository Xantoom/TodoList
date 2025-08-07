<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends WebTestCase
{
	use Factories, ResetDatabase;

	public function testUserListRequiresAdmin(): void
	{
		$client = static::createClient();

		$regularUser = UserFactory::createOne(['roles' => ['ROLE_USER']])->_real();
		$client->loginUser($regularUser);

		$client->request('GET', '/users');

		self::assertResponseStatusCodeSame(403);
	}

	public function testUserListAsAdmin(): void
	{
		$client = static::createClient();

		$adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN']])->_real();
		$client->loginUser($adminUser);

		$client->request('GET', '/users');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('user_list');
	}

	public function testUserCreateRequiresAdmin(): void
	{
		$client = static::createClient();

		$regularUser = UserFactory::createOne(['roles' => ['ROLE_USER']])->_real();
		$client->loginUser($regularUser);

		$client->request('GET', '/users/create');

		self::assertResponseStatusCodeSame(403);
	}

	public function testUserCreateAsAdmin(): void
	{
		$client = static::createClient();

		$adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN']])->_real();
		$client->loginUser($adminUser);

		$client->request('GET', '/users/create');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('user_create');
		self::assertSelectorExists('form');
	}

	public function testUserEditRequiresAdmin(): void
	{
		$client = static::createClient();

		$regularUser = UserFactory::createOne(['roles' => ['ROLE_USER']])->_real();
		$userToEdit = UserFactory::createOne()->_real();
		$client->loginUser($regularUser);

		$client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

		self::assertResponseStatusCodeSame(403);
	}

	public function testUserEditAsAdmin(): void
	{
		$client = static::createClient();

		$adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN']])->_real();
		$userToEdit = UserFactory::createOne()->_real();
		$client->loginUser($adminUser);

		$client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('user_edit');
		self::assertSelectorExists('form');
	}

	public function testUserCreateFormSubmission(): void
	{
		$client = static::createClient();

		$adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN']])->_real();
		$client->loginUser($adminUser);

		$crawler = $client->request('GET', '/users/create');

		// Find and submit the form
		$form = $crawler->selectButton('Ajouter')->form();
		$client->submit($form, [
			'user[username]' => 'newuser',
			'user[email]' => 'newuser@example.com',
			'user[password][first]' => 'password123',
			'user[password][second]' => 'password123',
			'user[roles]' => 'ROLE_USER'  // Single value, not array
		]);

		self::assertResponseRedirects('/users');

		// Follow redirect and check success message
		$client->followRedirect();
		self::assertSelectorTextContains('.alert-success', "L'utilisateur a bien été ajouté.");
	}

	public function testUserEditFormSubmission(): void
	{
		$client = static::createClient();

		$adminUser = UserFactory::createOne(['roles' => ['ROLE_ADMIN']])->_real();
		$userToEdit = UserFactory::createOne([
			'username' => 'oldusername',
			'email' => 'old@example.com'
		])->_real();
		$client->loginUser($adminUser);

		$crawler = $client->request('GET', '/users/' . $userToEdit->getId() . '/edit');

		// Find and submit the form
		$form = $crawler->selectButton('Modifier')->form();
		$client->submit($form, [
			'user[username]' => 'updatedusername',
			'user[email]' => 'updated@example.com',
			'user[password][first]' => 'newpassword123',
			'user[password][second]' => 'newpassword123',
			'user[roles]' => 'ROLE_USER'  // Single value, not array
		]);

		self::assertResponseRedirects('/users');

		// Follow redirect and check success message
		$client->followRedirect();
		self::assertSelectorTextContains('.alert-success', "L'utilisateur a bien été modifié");
	}
}
