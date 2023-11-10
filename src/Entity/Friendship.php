<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
class Friendship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'relationAsSender')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $friendA = null;

    #[ORM\ManyToOne(inversedBy: 'relationAsRecipient')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $friendB = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFriendA(): ?Profile
    {
        return $this->friendA;
    }

    public function setFriendA(?Profile $friendA): static
    {
        $this->friendA = $friendA;

        return $this;
    }

    public function getFriendB(): ?Profile
    {
        return $this->friendB;
    }

    public function setFriendB(?Profile $friendB): static
    {
        $this->friendB = $friendB;

        return $this;
    }


}
