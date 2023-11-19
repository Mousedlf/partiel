<?php

namespace App\Entity;

use App\Repository\PrivateMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrivateMessageRepository::class)]
class PrivateMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_privateConversations','show_privateConversationMessages'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sentPrivateMessages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_privateConversations','show_privateConversationMessages'])]
    private ?Profile $author = null;

    #[ORM\ManyToOne(inversedBy: 'privateMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrivateConversation $privateConversation = null;

    #[ORM\Column]
    #[Groups(['show_privateConversationMessages'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: Reaction::class)]
    #[Groups(['show_privateConversationMessages'])]
    private Collection $reactions;



    public function __construct()
    {
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrivateConversation(): ?PrivateConversation
    {
        return $this->privateConversation;
    }

    public function setPrivateConversation(?PrivateConversation $privateConversation): static
    {
        $this->privateConversation = $privateConversation;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): static
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): static
    {
        $this->reactions->removeElement($reaction);

        return $this;
    }


}
