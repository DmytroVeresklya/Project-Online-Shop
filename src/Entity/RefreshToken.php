<?php

namespace App\Entity;

use App\EntityDateTimeAwareInterface;
use App\EventListener\EntityDateTimeListener;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\AbstractRefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'refresh_token')]
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class RefreshToken extends AbstractRefreshToken implements EntityDateTimeAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'refresh_token_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(name: 'refresh_token', type: 'string', nullable: true)]
    protected $refreshToken;

    #[ORM\Column(name: 'username', type: 'string', nullable: true)]
    protected $username;

    #[ORM\Column(name: 'valid', type: 'datetime', nullable: true)]
    protected $valid;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private UserInterface $user;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
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

    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        /** @var RefreshToken $entity */
        $entity = parent::createForUserWithTtl($refreshToken, $user, $ttl);
        $entity->setUser($user);

        return $entity;
    }
}
