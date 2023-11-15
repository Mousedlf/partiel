<?php

namespace App\Entity;

use App\Repository\GroupConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupConversationRepository::class)]
class GroupConversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_groupConv'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'createdPublicConversations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_groupConv'])]
    private ?Profile $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'adminPublicConversations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_groupConv'])]
    private ?Profile $admin = null;

    #[ORM\ManyToMany(targetEntity: Profile::class, inversedBy: 'groupConversations')]
    #[Groups(['show_groupConv'])]
    private Collection $members;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: GroupMessage::class)]
    #[Groups(['show_privateConvMsgs'])]
    private Collection $messages;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdmin(): ?Profile
    {
        return $this->admin;
    }

    public function setAdmin(?Profile $admin): static
    {
        $this->admin = $admin;

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

    /**
     * @return Collection<int, GroupMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(GroupMessage $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(GroupMessage $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }
}
