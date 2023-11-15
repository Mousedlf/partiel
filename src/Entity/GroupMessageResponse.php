<?php

namespace App\Entity;

use App\Repository\GroupMessageResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupMessageResponseRepository::class)]
class GroupMessageResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_privateConvMsgs'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['show_privateConvMsgs'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'groupMessageResponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupMessage $groupMessage = null;

    #[ORM\ManyToOne(inversedBy: 'groupMessageResponses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_privateConvMsgs'])]
    private ?Profile $author = null;

    #[ORM\Column]
    #[Groups(['show_privateConvMsgs'])]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getGroupMessage(): ?GroupMessage
    {
        return $this->groupMessage;
    }

    public function setGroupMessage(?GroupMessage $groupMessage): static
    {
        $this->groupMessage = $groupMessage;

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
}
