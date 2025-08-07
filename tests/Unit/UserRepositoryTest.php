<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends TestCase
{
    public function testUpgradePassword(): void
    {
        $user = new User();
        $newHashedPassword = 'new_hashed_password';

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);

        $entityManager
            ->expects($this->once())
            ->method('flush');

        // Create a partial mock of UserRepository, only mocking getEntityManager
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityManager'])
            ->getMock();

        $userRepository
            ->expects($this->exactly(2)) // persist() and flush() both call getEntityManager()
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $userRepository->upgradePassword($user, $newHashedPassword);

        $this->assertSame($newHashedPassword, $user->getPassword());
    }

    public function testUpgradePasswordThrowsExceptionForUnsupportedUser(): void
    {
        $unsupportedUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

        // Create a real UserRepository instance for this test
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods([]) // Don't mock any methods, use real implementation
            ->getMock();

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage('Instances of "' . get_class($unsupportedUser) . '" are not supported.');

        $userRepository->upgradePassword($unsupportedUser, 'password');
    }
}
