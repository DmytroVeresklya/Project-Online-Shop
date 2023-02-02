<?php

namespace App\Entity;

use App\EntityDateTimeAwareInterface;
use App\EventListener\EntityDateTimeListener;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class User implements EntityDateTimeAwareInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'user_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

    #[ORM\Column(name: 'first_name', type: 'string', length: 255, nullable: true)]
    private ?string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string', length: 255, nullable: true)]
    private ?string $lastName;

    #[ORM\Column(name: 'phone_number', type: 'string', length: 255, nullable: false)]
    private string $phoneNumber;

    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'password', type: 'string', length: 64, nullable: false)]
    private string $password;

    #[ORM\Column(name: 'roles', type: 'simple_array', nullable: false, options: ['default' => 'ROLE_USER'])]
    private array $roles;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): DateTime
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(DateTime $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
