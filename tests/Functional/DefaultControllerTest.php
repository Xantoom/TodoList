<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
	public function testIndex(): void
	{
		$client = static::createClient();
		$user = new User()
			->setEmail('test@test.com')
			->setUsername('testuser')
			->setPassword('password123')
			->setRoles(['ROLE_USER'])
		;
		$entityManager = $client->getContainer()->get('doctrine')->getManager();
		$entityManager->persist($user);
		$entityManager->flush();
		$client->loginUser($user);
		$client->request('GET', '/');

		self::assertResponseIsSuccessful();
	}
}
