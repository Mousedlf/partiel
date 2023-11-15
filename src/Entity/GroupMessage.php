<?php

namespace App\Entity;

use App\Repository\GroupMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupMessageRepository::class)]
class GroupMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_privateConvMsgs'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['show_privateConvMsgs'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'groupMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_privateConvMsgs'])]
    private ?Profile $author = null;

    #[ORM\Column]
    #[Groups(['show_privateConvMsgs'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupConversation $conversation = null;

    #[ORM\OneToMany(mappedBy: 'groupMessage', targetEntity: GroupMessageResponse::class, orphanRemoval: true)]
    #[Groups(['show_privateConvMsgs'])]
    private Collection $groupMessageResponses;

    public function __construct()
    {
        $this->groupMessageResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?Profile
    {
        return $this->author;
    }

    public function setAuthor(?Profile $author): static
    {
        $this->author = $author;

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

    public function getConversation(): ?GroupConversation
    {
        return $this->conversation;
    }

    public function setConversation(?GroupConversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * @return Collection<int, GroupMessageResponse>
     */
    public function getGroupMessageResponses(): Collection
    {
        return $this->groupMessageResponses;
    }

    public function addGroupMessageResponse(GroupMessageResponse $groupMessageResponse): static
    {
        if (!$this->groupMessageResponses->contains($groupMessageResponse)) {
            $this->groupMessageResponses->add($groupMessageResponse);
            $groupMessageResponse->setGroupMessage($this);
        }

        return $this;
    }

    public function removeGroupMessageResponse(GroupMessageResponse $groupMessageResponse): static
    {
        if ($this->groupMessageResponses->removeElement($groupMessageResponse)) {
            // set the owning side to null (unless already changed)
            if ($groupMessageResponse->getGroupMessage() === $this) {
                $groupMessageResponse->setGroupMessage(null);
            }
        }

        return $this;
    }
}
