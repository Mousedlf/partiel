<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['events:read', 'profile-invitations:read', 'event-attending:read', 'event-admins:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['events:read'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['events:read', 'profile-invitations:read', 'event-attending:read', 'event-admins:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'organizedEvents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['events:read', 'profile-invitations:read', 'event-attending:read'])]
    private ?Profile $organiser = null;

    #[ORM\Column]
    #[Groups(['events:read'])]
    private ?bool $public = null;

    #[ORM\Column]
    #[Groups(['events:read'])]
    private ?bool $locationPublic = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['events:read', 'event-attending:read'])]
    private ?\DateTimeInterface $firstDay = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['events:read', 'event-attending:read'])]
    private ?\DateTimeInterface $lastDay = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'attendingEvents')]
    #[Groups(['event-participants:read'])]
    private Collection $participants;

    #[ORM\OneToMany(mappedBy: 'toEvent', targetEntity: Invitation::class, orphanRemoval: true)]
    #[Groups(['event-invitations:read'])]
    private Collection $sentInvitations;

    #[ORM\Column]
    private ?bool $canceled = null;

    #[ORM\OneToMany(mappedBy: 'toEvent', targetEntity: Contribution::class, orphanRemoval: true)]
    private Collection $contributions;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Suggestion::class, orphanRemoval: true)]
    private Collection $suggestions;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: AdminPrivateEvent::class)]
    #[Groups(['event-admins:read'])]
    private Collection $admins;


    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sentInvitations = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
        $this->admins = new ArrayCollection();
    }


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

    public function getFirstDay(): ?\DateTimeInterface
    {
        return $this->firstDay;
    }

    public function setFirstDay(\DateTimeInterface $firstDay): static
    {
        $this->firstDay = $firstDay;

        return $this;
    }

    public function getLastDay(): ?\DateTimeInterface
    {
        return $this->lastDay;
    }

    public function setLastDay(\DateTimeInterface $lastDay): static
    {
        $this->lastDay = $lastDay;

        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Profile $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Profile $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSentInvitations(): Collection
    {
        return $this->sentInvitations;
    }

    public function addSentInvitation(Invitation $sentInvitation): static
    {
        if (!$this->sentInvitations->contains($sentInvitation)) {
            $this->sentInvitations->add($sentInvitation);
            $sentInvitation->setToEvent($this);
        }

        return $this;
    }

    public function removeSentInvitation(Invitation $sentInvitation): static
    {
        if ($this->sentInvitations->removeElement($sentInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sentInvitation->getToEvent() === $this) {
                $sentInvitation->setToEvent(null);
            }
        }

        return $this;
    }

    public function isCanceled(): ?bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): static
    {
        $this->canceled = $canceled;

        return $this;
    }

    /**
     * @return Collection<int, Contribution>
     */
    public function getContributions(): Collection
    {
        return $this->contributions;
    }

    public function addContribution(Contribution $contribution): static
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions->add($contribution);
            $contribution->setToEvent($this);
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution): static
    {
        if ($this->contributions->removeElement($contribution)) {
            // set the owning side to null (unless already changed)
            if ($contribution->getToEvent() === $this) {
                $contribution->setToEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(Suggestion $suggestion): static
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions->add($suggestion);
            $suggestion->setEvent($this);
        }

        return $this;
    }

    public function removeSuggestion(Suggestion $suggestion): static
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getEvent() === $this) {
                $suggestion->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AdminPrivateEvent>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(AdminPrivateEvent $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->setEvent($this);
        }

        return $this;
    }

    public function removeAdmin(AdminPrivateEvent $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            // set the owning side to null (unless already changed)
            if ($admin->getEvent() === $this) {
                $admin->setEvent(null);
            }
        }

        return $this;
    }


}
