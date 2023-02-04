<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistException;
use App\ModelItem\SignUpRequest;
use App\Repository\UserRepository;
use App\Service\SignUpService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpServiceTest extends TestCase
{
    private UserPasswordHasherInterface $hasher;

    private UserRepository $userRepository;

    private AuthenticationSuccessHandler $successHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher         = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    public function testSignUpUserAlreadyExist(): void
    {
        $this->expectException(UserAlreadyExistException::class);

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true);

        $this->createService()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }

    public function testSignUp(): void
    {
        $response = new Response();
        $expectedHasherUser = (new User())
            ->setRoles(['ROLE_USER'])
            ->setFirstName('TestName')
            ->setLastName('TestName')
            ->setPhoneNumber('99843214')
            ->setEmail('test@test.com');

        $expectedUser = clone $expectedHasherUser;
        $expectedUser->setPassword('hashed_password');

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(false);

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($expectedHasherUser, 'testtest')
            ->willReturn('hashed_password');

        $this->userRepository->expects($this->once())
            ->method('save')
            ->with($expectedUser, true);

        $this->successHandler->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($expectedUser)
            ->willReturn($response);

        $signUpRequest = (new SignUpRequest())
            ->setFirstName('TestName')
            ->setLastName('TestName')
            ->setPhoneNumber('99843214')
            ->setEmail('test@test.com')
            ->setPassword('testtest')
            ->setConfirmPassword('testtest');

        $this->assertEquals($response, $this->createService()->signUp($signUpRequest));
    }

    private function createService(): SignUpService
    {
        return new SignUpService($this->hasher, $this->userRepository, $this->successHandler);
    }
}
