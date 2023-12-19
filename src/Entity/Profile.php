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
    #[Groups(['profiles:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ofUser = null;

    #[ORM\Column(length: 255)]
    #[Groups(['profiles:read', 'events:read', 'event-participants:read', 'event-invitations:read', 'profile-invitations:read', 'event-attending:read', 'contributions:read', 'suggestions:read', 'event-admins:read'])]
    private ?string $username = null;


//    EVENTS
    #[ORM\OneToMany(mappedBy: 'organiser', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $organizedEvents;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    private Collection $attendingEvents;


//    INVITATIONS
    #[ORM\OneToMany(mappedBy: 'toProfile', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $receivedInvitations;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Contribution::class)]
    private Collection $contributionsPrivateEvent;


//    SUGGESTIONS
    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Suggestion::class, orphanRemoval: true)]
    private Collection $suggestionsPrivateEvent;

    #[ORM\OneToMany(mappedBy: 'takenBy', targetEntity: Suggestion::class)]
    private Collection $takenSuggestions;

    #[ORM\OneToMany(mappedBy: 'handledSuggestionBy', targetEntity: Contribution::class)]
    private Collection $handledSuggestions;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    private ?Image $image = null;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: AdminPrivateEvent::class, orphanRemoval: true)]
    private Collection $adminOfPrivateEvent;


    public function __construct()
    {
        $this->organizedEvents = new ArrayCollection();
        $this->attendingEvents = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
        $this->contributionsPrivateEvent = new ArrayCollection();
        $this->suggestionsPrivateEvent = new ArrayCollection();
        $this->takenSuggestions = new ArrayCollection();
        $this->handledSuggestions = new ArrayCollection();
        $this->adminOfPrivateEvent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getOrganizedEvents(): Collection
    {
        return $this->organizedEvents;
    }

    public function addOrganizedEvent(Event $organizedEvent): static
    {
        if (!$this->organizedEvents->contains($organizedEvent)) {
            $this->organizedEvents->add($organizedEvent);
            $organizedEvent->setOrganiser($this);
        }

        return $this;
    }

    public function removeOrganizedEvent(Event $organizedEvent): static
    {
        if ($this->organizedEvents->removeElement($organizedEvent)) {
            // set the owning side to null (unless already changed)
            if ($organizedEvent->getOrganiser() === $this) {
                $organizedEvent->setOrganiser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getAttendingEvents(): Collection
    {
        return $this->attendingEvents;
    }

    public function addAttendingEvent(Event $attendingEvent): static
    {
        if (!$this->attendingEvents->contains($attendingEvent)) {
            $this->attendingEvents->add($attendingEvent);
            $attendingEvent->addParticipant($this);
        }

        return $this;
    }

    public function removeAttendingEvent(Event $attendingEvent): static
    {
        if ($this->attendingEvents->removeElement($attendingEvent)) {
            $attendingEvent->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitation $receivedInvitation): static
    {
        if (!$this->receivedInvitations->contains($receivedInvitation)) {
            $this->receivedInvitations->add($receivedInvitation);
            $receivedInvitation->setToProfile($this);
        }

        return $this;
    }

    public function removeReceivedInvitation(Invitation $receivedInvitation): static
    {
        if ($this->receivedInvitations->removeElement($receivedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvitation->getToProfile() === $this) {
                $receivedInvitation->setToProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contribution>
     */
    public function getContributionsPrivateEvent(): Collection
    {
        return $this->contributionsPrivateEvent;
    }

    public function addContributionsPrivateEvent(Contribution $contributionsPrivateEvent): static
    {
        if (!$this->contributionsPrivateEvent->contains($contributionsPrivateEvent)) {
            $this->contributionsPrivateEvent->add($contributionsPrivateEvent);
            $contributionsPrivateEvent->setCreatedBy($this);
        }

        return $this;
    }

    public function removeContributionsPrivateEvent(Contribution $contributionsPrivateEvent): static
    {
        if ($this->contributionsPrivateEvent->removeElement($contributionsPrivateEvent)) {
            // set the owning side to null (unless already changed)
            if ($contributionsPrivateEvent->getCreatedBy() === $this) {
                $contributionsPrivateEvent->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getSuggestionsPrivateEvent(): Collection
    {
        return $this->suggestionsPrivateEvent;
    }

    public function addSuggestionsPrivateEvent(Suggestion $suggestionsPrivateEvent): static
    {
        if (!$this->suggestionsPrivateEvent->contains($suggestionsPrivateEvent)) {
            $this->suggestionsPrivateEvent->add($suggestionsPrivateEvent);
            $suggestionsPrivateEvent->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSuggestionsPrivateEvent(Suggestion $suggestionsPrivateEvent): static
    {
        if ($this->suggestionsPrivateEvent->removeElement($suggestionsPrivateEvent)) {
            // set the owning side to null (unless already changed)
            if ($suggestionsPrivateEvent->getCreatedBy() === $this) {
                $suggestionsPrivateEvent->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Suggestion>
     */
    public function getTakenSuggestions(): Collection
    {
        return $this->takenSuggestions;
    }

    public function addTakenSuggestion(Suggestion $takenSuggestion): static
    {
        if (!$this->takenSuggestions->contains($takenSuggestion)) {
            $this->takenSuggestions->add($takenSuggestion);
            $takenSuggestion->setTakenBy($this);
        }

        return $this;
    }

    public function removeTakenSuggestion(Suggestion $takenSuggestion): static
    {
        if ($this->takenSuggestions->removeElement($takenSuggestion)) {
            // set the owning side to null (unless already changed)
            if ($takenSuggestion->getTakenBy() === $this) {
                $takenSuggestion->setTakenBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contribution>
     */
    public function getHandledSuggestions(): Collection
    {
        return $this->handledSuggestions;
    }

    public function addHandledSuggestion(Contribution $handledSuggestion): static
    {
        if (!$this->handledSuggestions->contains($handledSuggestion)) {
            $this->handledSuggestions->add($handledSuggestion);
            $handledSuggestion->setHandledSuggestionBy($this);
        }

        return $this;
    }

    public function removeHandledSuggestion(Contribution $handledSuggestion): static
    {
        if ($this->handledSuggestions->removeElement($handledSuggestion)) {
            // set the owning side to null (unless already changed)
            if ($handledSuggestion->getHandledSuggestionBy() === $this) {
                $handledSuggestion->setHandledSuggestionBy(null);
            }
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, AdminPrivateEvent>
     */
    public function getAdminOfPrivateEvent(): Collection
    {
        return $this->adminOfPrivateEvent;
    }

    public function addAdminOfPrivateEvent(AdminPrivateEvent $adminOfPrivateEvent): static
    {
        if (!$this->adminOfPrivateEvent->contains($adminOfPrivateEvent)) {
            $this->adminOfPrivateEvent->add($adminOfPrivateEvent);
            $adminOfPrivateEvent->setProfile($this);
        }

        return $this;
    }

    public function removeAdminOfPrivateEvent(AdminPrivateEvent $adminOfPrivateEvent): static
    {
        if ($this->adminOfPrivateEvent->removeElement($adminOfPrivateEvent)) {
            // set the owning side to null (unless already changed)
            if ($adminOfPrivateEvent->getProfile() === $this) {
                $adminOfPrivateEvent->setProfile(null);
            }
        }

        return $this;
    }

}
