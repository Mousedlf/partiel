<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['events:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['events:read'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['events:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'organizedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['events:read'])]
    private ?Profile $organiser = null;

    #[ORM\Column]
    #[Groups(['events:read'])]
    private ?bool $public = null;

    #[ORM\Column]
    #[Groups(['events:read'])]
    private ?bool $locationPublic = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOrganiser(): ?Profile
    {
        return $this->organiser;
    }

    public function setOrganiser(?Profile $organiser): static
    {
        $this->organiser = $organiser;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    public function isLocationPublic(): ?bool
    {
        return $this->locationPublic;
    }

    public function setLocationPublic(bool $locationPublic): static
    {
        $this->locationPublic = $locationPublic;

        return $this;
    }
}
