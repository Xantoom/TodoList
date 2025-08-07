<?php

namespace App\Tests\Unit;

use App\Controller\SecurityController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    public function testLogoutThrowsLogicException(): void
    {
        $controller = new SecurityController();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank - it will be intercepted by the logout key on your firewall.');

        $controller->logout();
    }

    public function testLoginMethodExists(): void
    {
        $authUtils = $this->createMock(AuthenticationUtils::class);
        $authUtils->method('getLastAuthenticationError')->willReturn(null);
        $authUtils->method('getLastUsername')->willReturn('');

        $controller = $this->getMockBuilder(SecurityController::class)
            ->onlyMethods(['render'])
            ->getMock();

        $controller->expects($this->once())
            ->method('render')
            ->with('security/login.html.twig', [
                'last_username' => '',
                'error' => null,
            ]);

        $controller->login($authUtils);
    }
}
