<?php

namespace App\Entity;

use App\Repository\ReactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
class Reaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_reactionTypes', 'show_privateConversationMessages', 'show_privateConvMsgs'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['show_reactionTypes', 'show_privateConversationMessages', 'show_privateConvMsgs'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'reactions')]
    #[Groups(['show_privateConversationMessages', 'show_privateConvMsgs'])]
    private ?Profile $author = null;



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

    public function getAuthor(): ?Profile
    {
        return $this->author;
    }

    public function setAuthor(?Profile $author): static
    {
        $this->author = $author;

        return $this;
    }



}
