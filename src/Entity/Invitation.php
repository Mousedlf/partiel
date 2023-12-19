<?php

namespace App\Entity;

use App\Repository\InvitationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['profile-invitations:read', 'event-invitations:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sentInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('profile-invitations:read')]
    private ?Event $toEvent = null;

    #[ORM\ManyToOne(inversedBy: 'receivedInvitations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['event-invitations:read'])]
    private ?Profile $toProfile = null;

    #[ORM\Column(length: 255)]
    #[Groups(['event-invitations:read'])]
    private ?string $status = "pending";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToEvent(): ?Event
    {
        return $this->toEvent;
    }

    public function setToEvent(?Event $toEvent): static
    {
        $this->toEvent = $toEvent;

        return $this;
    }

    public function getToProfile(): ?Profile
    {
        return $this->toProfile;
    }

    public function setToProfile(?Profile $toProfile): static
    {
        $this->toProfile = $toProfile;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
