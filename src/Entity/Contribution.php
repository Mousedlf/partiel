<?php

namespace App\Entity;

use App\Repository\ContributionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContributionRepository::class)]
class Contribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['contributions:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['contributions:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['contributions:read'])]
    private ?string $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'contributionsPrivateEvent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['contributions:read'])]
    private ?Profile $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $toEvent = null;

    #[ORM\ManyToOne(inversedBy: 'handledSuggestions')]
    #[Groups(['contributions:read'])]
    private ?Profile $handledSuggestionBy = null;



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

    public function getCreatedBy(): ?Profile
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Profile $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
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

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getHandledSuggestionBy(): ?Profile
    {
        return $this->handledSuggestionBy;
    }

    public function setHandledSuggestionBy(?Profile $handledSuggestionBy): static
    {
        $this->handledSuggestionBy = $handledSuggestionBy;

        return $this;
    }
}
