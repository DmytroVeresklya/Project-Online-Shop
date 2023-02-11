<?php

namespace App\Tests\Security;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\JwtUserProvider;
use App\Tests\AbstractTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUserProviderTest extends AbstractTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testSupportClass(): void
    {
        $user = (new User())->setEmail('test@test.com');
        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn($user);

        $this->assertEquals($user, $provider->loadUserByIdentifier('test@test.com'));
    }

    public function testLoadUserByIdentifierNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@test.com'])
            ->willReturn(null);

        (new JwtUserProvider($this->userRepository))->loadUserByIdentifier('test@test.com');
    }

    public function testLoadUserByIdentifierAndPayload(): void
    {
        $user = (new User())->setEmail('test@test.com');
        $provider = new JwtUserProvider($this->userRepository);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '12'])
            ->willReturn($user);

        $this->assertEquals($user, $provider->loadUserByIdentifierAndPayload('id', ['id' => 12]));
    }

    public function testLoadUserByIdentifierAndPayloadNotFoundException(): void
    {
        $this->expectException(UserNotFoundException::class);

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '11'])
            ->willReturn(null);

        (new JwtUserProvider($this->userRepository))->loadUserByIdentifierAndPayload('id', ['id' => '11']);
    }

    public function testSupportsClass(): void
    {
        $this->assertTrue((new JwtUserProvider($this->userRepository))->supportsClass(User::class));
        $this->assertFalse((new JwtUserProvider($this->userRepository))->supportsClass(Product::class));
    }

    public function testLoadUserByUsernameAndPayload()
    {
        $this->assertNull((new JwtUserProvider($this->userRepository))->loadUserByUsernameAndPayload('', []));
    }

    public function testRefreshUser()
    {
        $user = $this->createMock(UserInterface::class);

        $this->assertNull((new JwtUserProvider($this->userRepository))->refreshUser($user));
    }
}
