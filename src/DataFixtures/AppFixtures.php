<?php

namespace App\DataFixtures;

use App\Factory\TaskFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager): void
    {
		// Hashed password for 'password'
		$password = '$2y$04$tlNLH1lxtphb3FIsjiKGl.OxrjfNdoXlOmlWHRdBl3tYoZbVn5kpS';

		$testUser = UserFactory::createOne([
			'username' => 'Test123',
			'email' => 'test@test.com',
			'roles' => ['ROLE_USER'],
			'password' => $password,
		])->_real();

		$adminUser = UserFactory::createOne([
			'username' => 'Admin',
			'email' => 'admin@test.com',
			'roles' => ['ROLE_ADMIN'],
			'password' => $password,
		])->_real();

		TaskFactory::createMany(10, [
			'content' => faker()->text(100),
			'createdAt' => new \DateTimeImmutable('-1 month'),
			'isDone' => faker()->boolean(30),
			'title' => faker()->sentence(8),
			'userEntity' => $testUser,
		]);

		TaskFactory::createMany(5, [
			'content' => faker()->text(100),
			'createdAt' => new \DateTimeImmutable('-1 month'),
			'isDone' => faker()->boolean(30),
			'title' => faker()->sentence(8),
			'userEntity' => $adminUser,
		]);
    }
}
