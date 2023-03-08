<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpService
{
    public function __construct(
        private readonly UserPasswordHasherInterface  $hasher,
        private readonly UserRepository               $userRepository,
        private readonly AuthenticationSuccessHandler $successHandler,
    ) {
    }

    public function signUp(SignUpRequest $request): Response
    {
        if ($this->userRepository->existsByEmail($request->getEmail())) {
            throw new UserAlreadyExistException();
        }

        $user = (new User())
            ->setRoles(['ROLE_USER'])
            ->setFirstName($request->getFirstName())
            ->setLastName($request->getLastName())
            ->setPhoneNumber($request->getPhoneNumber())
            ->setEmail($request->getEmail());

        $user->setPassword($this->hasher->hashPassword($user, $request->getPassword()));

        $this->userRepository->save($user, true);

        return $this->successHandler->handleAuthenticationSuccess($user);
    }
}
