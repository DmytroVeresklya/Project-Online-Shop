<?php

namespace App\Entity;

use App\EventListener\EntityDateTimeListener;
use App\Repository\ConversationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Index(columns: ['last_message_id'], name: 'last_message_id_index')]
#[ORM\EntityListeners([
    EntityDateTimeListener::class,
])]
class Conversation implements EntityDateTimeAwareInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'conversation_id', allocationSize: 1, initialValue: 100)]
    #[ORM\Column]
    private int $id;

    #[ORM\OneToMany(mappedBy: "conversation", targetEntity: Participant::class)]
    private Collection $participants;

    #[ORM\OneToOne(targetEntity: Message::class)]
    #[ORM\JoinColumn(name: 'last_message_id', referencedColumnName: 'id')]
    private ?Message $lastMessage;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(name: 'modified_at', type: 'datetime')]
    private DateTime $modifiedAt;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setConversation($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getConversation() === $this) {
                $participant->setConversation(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

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
