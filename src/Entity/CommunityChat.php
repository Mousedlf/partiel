<?php

namespace App\Entity;

use App\Repository\CommunityChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommunityChatRepository::class)]
class CommunityChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_communities'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['show_communities'])]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'createdCommunityChats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $createdBy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'joinedCommunityChats')]
    #[Groups(['show_communities'])]
    private Collection $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Profile $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(Profile $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }
}
