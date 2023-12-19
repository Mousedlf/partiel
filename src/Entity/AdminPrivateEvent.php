<?php

namespace App\Entity;

use App\Repository\AdminPrivateEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AdminPrivateEventRepository::class)]
class AdminPrivateEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event-admins:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adminOfPrivateEvent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['event-admins:read'])]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}
