<?php

namespace App\Entity;

use App\EventListener\EntityDateTimeListener;
use App\Repository\SubscriberRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class Subscriber implements EntityDateTimeAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'subscriber_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

    public function getId(): ?int
    {
        return $this->id;
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
}
