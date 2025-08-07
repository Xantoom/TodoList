<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
	use Factories, ResetDatabase;

	public function testLoginPageIsAccessible(): void
	{
		$client = static::createClient();

		$client->request('GET', '/login');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('app_login');
		self::assertSelectorExists('form');
		self::assertSelectorExists('input[name="_username"]');
		self::assertSelectorExists('input[name="_password"]');
		self::assertSelectorExists('input[name="_csrf_token"]');
	}

	public function testLoginWithValidCredentials(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne([
			'username' => 'testuser',
			'password' => '$2y$04$tlNLH1lxtphb3FIsjiKGl.OxrjfNdoXlOmlWHRdBl3tYoZbVn5kpS' // 'password'
		]);

		$crawler = $client->request('GET', '/login');

		$form = $crawler->selectButton('Se connecter')->form();
		$client->submit($form, [
			'_username' => 'testuser',
			'_password' => 'password'
		]);

		self::assertResponseRedirects('/');
	}

	public function testLoginWithInvalidCredentials(): void
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/login');

		$form = $crawler->selectButton('Se connecter')->form();
		$client->submit($form, [
			'_username' => 'wronguser',
			'_password' => 'wrongpassword'
		]);

		self::assertResponseRedirects('/login');

		$client->followRedirect();
		self::assertSelectorExists('.alert-danger');
	}

	public function testLogoutRedirectsToLogin(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/logout');

		self::assertResponseRedirects();
	}

	public function testLoginPageShowsLastUsername(): void
	{
		$client = static::createClient();

		// First, make a failed login attempt
		$crawler = $client->request('GET', '/login');
		$form = $crawler->selectButton('Se connecter')->form();
		$client->submit($form, [
			'_username' => 'testuser',
			'_password' => 'wrongpassword'
		]);

		// Follow redirect back to login page
		$crawler = $client->followRedirect();

		// Check that the username field contains the last attempted username
		$usernameField = $crawler->filter('input[name="_username"]');
		self::assertEquals('testuser', $usernameField->attr('value'));
	}

	public function testRedirectToLoginWhenNotAuthenticated(): void
	{
		$client = static::createClient();

		$client->request('GET', '/');

		self::assertResponseRedirects('/login');
	}
}
