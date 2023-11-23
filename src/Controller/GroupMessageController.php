<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Entity\GroupMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/group')]
class GroupMessageController extends AbstractController
{
    #[Route('/message/in/{convId}', methods:['POST'])]
    public function newMessage(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, GroupConversation $conversation): Response
    {
        $members = $conversation->getMembers();
        foreach($members as $member){
            if($this->getUser()->getProfile() !== $member){
                return $this->json("you are not part of the group", 401);
            }

            $json = $request->getContent();
            $message = $serializer->deserialize($json, GroupMessage::class, 'json');

            $message->setAuthor($this->getUser()->getProfile());
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setConversation($conversation);

            $manager->persist($message);
            $manager->flush();

            return $this->json("message sent", 200);
        }
    }

    #[Route('/{convId}/delete/{messageId}', methods:['DELETE'])]
    public function deleteMessage(
        #[MapEntity(id: 'convId')] GroupConversation $groupConversation,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        EntityManagerInterface $manager): Response
    {
        //autres verifications ?

        if($message->getAuthor() != $this->getUser()->getProfile()){
            return $this->json("not yours to delete", 401);
        }

        $manager->remove($message);
        $manager->flush();

        return $this->json("message deleted", 200);
    }

    #[Route('/{convId}/edit/{messageId}', methods:['PUT'])]
    public function editMessage(
        #[MapEntity(id: 'convId')] GroupConversation $groupConversation,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        EntityManagerInterface $manager,Request $request, SerializerInterface $serializer): Response
    {
        //autres verifications ?

        if($message->getAuthor() != $this->getUser()->getProfile()){
            return $this->json("not yours to change", 401);
        }

        $editedMessage =$serializer->deserialize($request->getContent(), GroupMessage::class, 'json');
        $message->setContent($editedMessage->getContent());

        $manager->persist($message);
        $manager->flush();

        return $this->json("message edited", 200);
    }


}
