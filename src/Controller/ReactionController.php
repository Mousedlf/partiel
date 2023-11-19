<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Entity\GroupMessage;
use App\Entity\PrivateConversation;
use App\Entity\PrivateMessage;
use App\Entity\Reaction;
use App\Repository\ReactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ReactionController extends AbstractController
{
    #[Route('/reactions', methods: ['GET'])]
    public function indexReactionType(ReactionRepository $repository):Response
    {
       $reactions = $repository->findAll();
       return $this->json($reactions, 201, [],['groups'=>'show_reactionTypes']);

    }

    #[Route('/reaction/new', methods: ['POST'])]
    public function newReactionType(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager):Response
    {
        $json = $request->getContent();
        $reaction = $serializer->deserialize($json,Reaction::class, 'json');

        $manager->persist($reaction);
        $manager->flush();
        return $this->json("new reaction type added", 201);

    }

# PRIVATE MESSAGE --------------------------------------------------------------------------------------------

    #[Route('/private/conversation/{convId}/message/{messageId}/reaction/{id}')]
    public function reactToPrivateMessage(
        #[MapEntity(id: 'id')] Reaction $reaction,
        #[MapEntity(id: 'messageId')] PrivateMessage $message,
        #[MapEntity(id: 'convId')] PrivateConversation $conversation,
        EntityManagerInterface $manager): Response
    {
        $currentUser= $this->getUser()->getProfile();

        if($conversation->getParticipantB() == $currentUser or $conversation->getParticipantA() == $currentUser ){
            $reaction->setAuthor($this->getUser()->getProfile());
            $message->addReaction($reaction);
            $manager->persist($message);
            $manager->flush();
            return $this->json("reaction added to message", 201);
        }

        return $this->json("not one of your private conversations", 401);
    }

    #[Route('/private/conversation/{convId}/message/{messageId}/reaction/{id}/remove', methods: (['DELETE']))]
    public function removeReactionToPrivateMessage(
        #[MapEntity(id: 'id')] Reaction $reaction,
        #[MapEntity(id: 'messageId')] PrivateMessage $message,
        #[MapEntity(id: 'convId')] PrivateConversation $conversation,
        EntityManagerInterface $manager): Response
    {
        if($this->getUser()->getProfile() == $reaction->getAuthor()){
            $message->removeReaction($reaction);
            $manager->persist($message);
            $manager->flush();
            return $this->json("reaction removed from message", 201);
        };

        return $this->json("not one of your reactions", 401);
    }

    # GROUP MESSAGE --------------------------------------------------------------------------------------------

    #[Route('/group/conversation/{convId}/message/{messageId}/reaction/{id}')]
    public function reactToGroupMessage(
        #[MapEntity(id: 'id')] Reaction $reaction,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        EntityManagerInterface $manager): Response
    {
        $currentUser= $this->getUser()->getProfile();

        if($conversation->getMembers()->contains($currentUser) ){
            $reaction->setAuthor($this->getUser()->getProfile());
            $message->addReaction($reaction);
            $manager->persist($message);
            $manager->flush();
            return $this->json("reaction added to message", 201);
        }

        return $this->json("you are not part of the group conversations", 401);
    }

    #[Route('/group/conversation/{convId}/message/{messageId}/reaction/{id}/remove')]
    public function removeReactionToGroupMessage(
        #[MapEntity(id: 'id')] Reaction $reaction,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        EntityManagerInterface $manager): Response
    {
        if($this->getUser()->getProfile() == $reaction->getAuthor()){
            $message->removeReaction($reaction);
            $manager->persist($message);
            $manager->flush();
            return $this->json("reaction removed from message", 201);
        }

        return $this->json("not your reaction", 401);
    }
}
