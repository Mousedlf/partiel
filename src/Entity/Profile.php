<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sentBy', 'show_requests', "show_profiles"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['sentBy', 'show_requests', "show_profiles"])]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_requests', "show_profiles"])]
    private ?User $ofUser = null;

    #[ORM\OneToMany(mappedBy: 'ofProfile', targetEntity: FriendRequest::class)]
    #[Groups(['sentBy', "show_profiles"])]
    private Collection $receivedFriendRequests;

    #[ORM\OneToMany(mappedBy: 'toProfile', targetEntity: FriendRequest::class)]
    private Collection $sentFriendRequests;

    #[ORM\OneToMany(mappedBy: 'friendA', targetEntity: Friendship::class)]
    private Collection $relationAsSender;

    #[ORM\OneToMany(mappedBy: 'friendB', targetEntity: Friendship::class)]
    private Collection $relationAsRecipient;


    public function __construct()
    {
        $this->receivedFriendRequests = new ArrayCollection();
        $this->sentFriendRequests = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->relationAsSender = new ArrayCollection();
        $this->relationB = new ArrayCollection();
        $this->relationAsRecipient = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOfUser(): ?User
    {
        return $this->ofUser;
    }

    public function setOfUser(User $ofUser): static
    {
        $this->ofUser = $ofUser;

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getReceivedFriendRequests(): Collection
    {
        return $this->receivedFriendRequests;
    }

    public function addReceivedFriendRequest(FriendRequest $receivedFriendRequest): static
    {
        if (!$this->receivedFriendRequests->contains($receivedFriendRequest)) {
            $this->receivedFriendRequests->add($receivedFriendRequest);
            $receivedFriendRequest->setOfProfile($this);
        }

        return $this;
    }

    public function removeReceivedFriendRequest(FriendRequest $receivedFriendRequest): static
    {
        if ($this->receivedFriendRequests->removeElement($receivedFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($receivedFriendRequest->getOfProfile() === $this) {
                $receivedFriendRequest->setOfProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FriendRequest>
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->sentFriendRequests;
    }

    public function addSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if (!$this->sentFriendRequests->contains($sentFriendRequest)) {
            $this->sentFriendRequests->add($sentFriendRequest);
            $sentFriendRequest->setToProfile($this);
        }

        return $this;
    }

    public function removeSentFriendRequest(FriendRequest $sentFriendRequest): static
    {
        if ($this->sentFriendRequests->removeElement($sentFriendRequest)) {
            // set the owning side to null (unless already changed)
            if ($sentFriendRequest->getToProfile() === $this) {
                $sentFriendRequest->setToProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getRelationAsSender(): Collection
    {
        return $this->relationAsSender;
    }

    public function addRelationAsSender(Friendship $relationAsSender): static
    {
        if (!$this->relationAsSender->contains($relationAsSender)) {
            $this->relationAsSender->add($relationAsSender);
            $relationAsSender->setFriendA($this);
        }

        return $this;
    }

    public function removeRelationAsSender(Friendship $relationAsSender): static
    {
        if ($this->relationAsSender->removeElement($relationAsSender)) {
            // set the owning side to null (unless already changed)
            if ($relationAsSender->getFriendA() === $this) {
                $relationAsSender->setFriendA(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getRelationAsRecipient(): Collection
    {
        return $this->relationAsRecipient;
    }

    public function addRelationAsRecipient(Friendship $relationAsRecipient): static
    {
        if (!$this->relationAsRecipient->contains($relationAsRecipient)) {
            $this->relationAsRecipient->add($relationAsRecipient);
            $relationAsRecipient->setFriendB($this);
        }

        return $this;
    }

    public function removeRelationAsRecipient(Friendship $relationAsRecipient): static
    {
        if ($this->relationAsRecipient->removeElement($relationAsRecipient)) {
            // set the owning side to null (unless already changed)
            if ($relationAsRecipient->getFriendB() === $this) {
                $relationAsRecipient->setFriendB(null);
            }
        }

        return $this;
    }




}
