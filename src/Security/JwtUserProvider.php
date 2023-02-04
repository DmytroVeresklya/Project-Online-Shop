<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUserProvider implements PayloadAwareUserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getUser('email', $identifier);
    }

    public function loadUserByIdentifierAndPayload($identifier, array $payload): UserInterface
    {
        return $this->getUser('id', $payload['id']);
    }

    private function getUser($key, $value): UserInterface
    {
        $user = $this->userRepository->findOneBy([$key => $value]);

        if (null === $user) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $value));
            $e->setUserIdentifier($value);

            throw $e;
        }

        return $user;
    }

    public function loadUserByUsernameAndPayload(string $username, array $payload): ?UserInterface
    {
        return null;
    }

    public function refreshUser(UserInterface $user): ?UserInterface
    {
        return null;
    }
}
