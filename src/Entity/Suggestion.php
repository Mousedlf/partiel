<?php

namespace App\Entity;

use App\Repository\SuggestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SuggestionRepository::class)]
class Suggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['suggestions:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['suggestions:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['suggestions:read'])]
    private ?bool $taken = null;

    #[ORM\ManyToOne(inversedBy: 'suggestionsPrivateEvent')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'suggestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'takenSuggestions')]
    #[Groups(['suggestions:read'])]
    private ?Profile $takenBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isTaken(): ?bool
    {
        return $this->taken;
    }

    public function setTaken(bool $taken): static
    {
        $this->taken = $taken;

        return $this;
    }

    public function getCreatedBy(): ?Profile
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Profile $createdBy): static
    {
        $this->createdBy = $createdBy;

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

    public function getTakenBy(): ?Profile
    {
        return $this->takenBy;
    }

    public function setTakenBy(?Profile $takenBy): static
    {
        $this->takenBy = $takenBy;

        return $this;
    }
}
