<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('show_requests', 'create_user')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receivedFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('show_requests')]
    private ?Profile $ofProfile = null;

    #[ORM\ManyToOne(inversedBy: 'sentFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sentBy', 'show_requests', 'show_profiles'])]
    private ?Profile $toProfile = null;

    #[ORM\Column]
    #[Groups('show_requests')]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfProfile(): ?Profile
    {
        return $this->ofProfile;
    }

    public function setOfProfile(?Profile $ofProfile): static
    {
        $this->ofProfile = $ofProfile;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
