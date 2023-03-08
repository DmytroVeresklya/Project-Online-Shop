<?php

namespace App\Entity;

use App\EventListener\EntityDateTimeListener;
use App\Repository\ParticipantRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class Participant implements EntityDateTimeAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'participant_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participants')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    private Conversation $conversation;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
