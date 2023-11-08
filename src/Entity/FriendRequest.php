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
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receivedFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $ofProfile = null;

    #[ORM\ManyToOne(inversedBy: 'sentFriendRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sentBy'])]
    private ?Profile $toProfile = null;

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
}
