<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	private UserPasswordHasherInterface $userPasswordHasher;

	public function __construct(
		UserPasswordHasherInterface $userPasswordHasher
	) {
		$this->userPasswordHasher = $userPasswordHasher;
	}

	public function load(ObjectManager $manager): void
    {
		$password = 'password';

        $user = new User()
	        ->setUsername('Test123')
	        ->setEmail('test@test.com')
	        ->setRoles(['ROLE_USER'])
        ;

		$hashedPassword = $this->userPasswordHasher->hashPassword($user, $password);
		$user->setPassword($hashedPassword);

		$manager->persist($user);

		for ($i = 1; $i <= 10; $i++) {
			$task = new Task()
				->setContent('Le contenu de la task n°' . $i)
				->setCreatedAt(new \DateTimeImmutable())
				->setIsDone($i % 2 === 0)
				->setTitle('Task ' . $i)
			;

			$manager->persist($task);
		}

        $manager->flush();
    }
}
