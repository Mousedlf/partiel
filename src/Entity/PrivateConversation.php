<?php

namespace App\Entity;

use App\Repository\PrivateConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrivateConversationRepository::class)]
class PrivateConversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["show_profiles", 'show_privateConversations'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participantAOfPrivateChat')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["show_profiles", 'show_privateConversations'])]
    private ?Profile $participantA = null;

    #[ORM\ManyToOne(inversedBy: 'participantBOfPrivateChat')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["show_profiles", 'show_privateConversations'])]
    private ?Profile $participantB = null;

    #[ORM\OneToMany(mappedBy: 'privateConversation', targetEntity: PrivateMessage::class)]
    #[Groups(['show_privateConversations'])]
    private Collection $privateMessages;

    public function __construct()
    {
        $this->privateMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipantA(): ?Profile
    {
        return $this->participantA;
    }

    public function setParticipantA(?Profile $participantA): static
    {
        $this->participantA = $participantA;

        return $this;
    }

    public function getParticipantB(): ?Profile
    {
        return $this->participantB;
    }

    public function setParticipantB(?Profile $participantB): static
    {
        $this->participantB = $participantB;

        return $this;
    }

    /**
     * @return Collection<int, PrivateMessage>
     */
    public function getPrivateMessages(): Collection
    {
        return $this->privateMessages;
    }

    public function addPrivateMessage(PrivateMessage $privateMessage): static
    {
        if (!$this->privateMessages->contains($privateMessage)) {
            $this->privateMessages->add($privateMessage);
            $privateMessage->setPrivateConversation($this);
        }

        return $this;
    }

    public function removePrivateMessage(PrivateMessage $privateMessage): static
    {
        if ($this->privateMessages->removeElement($privateMessage)) {
            // set the owning side to null (unless already changed)
            if ($privateMessage->getPrivateConversation() === $this) {
                $privateMessage->setPrivateConversation(null);
            }
        }

        return $this;
    }
}
