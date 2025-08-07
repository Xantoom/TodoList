<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DefaultControllerTest extends WebTestCase
{
	use Factories, ResetDatabase;

	public function testIndexAction(): void
	{
		$client = static::createClient();

		$user = UserFactory::createOne()->_real();
		$client->loginUser($user);

		$client->request('GET', '/');

		self::assertResponseIsSuccessful();
		self::assertRouteSame('homepage');
	}
}
