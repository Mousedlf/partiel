<?php

namespace App\Controller;

use App\Entity\PrivateConversation;
use App\Entity\PrivateMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/private')]
class PrivateMessageController extends AbstractController
{
    #[Route('/message/in/{id}', name: 'new_private_message', methods:['POST'])]
    public function newMessage(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, PrivateConversation $privateConversation): Response
    {
        $json = $request->getContent();
        $message = $serializer->deserialize($json, PrivateMessage::class, 'json');

        $message->setAuthor($this->getUser()->getProfile());
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setPrivateConversation($privateConversation);


        $manager->persist($message);
        $manager->flush();

        return $this->json("message sent", 200);
    }

    #[Route('/in/{convId}/delete/{messageId}', methods:['DELETE'])]
    public function deleteMessage(
        #[MapEntity(id: 'convId')] PrivateConversation $privateConversation,
        #[MapEntity(id: 'messageId')] PrivateMessage $message,
        EntityManagerInterface $manager): JsonResponse
    {

        # verif si message existe fonctionne pas # de meme pour la conv
        if(!$message){
            return $this->json("trying to remove something that isn't there genius", 401);
        }

        # verif si bien auteur du message
        if($message->getAuthor() != $this->getUser()->getProfile()){
            return $this->json("not yours to delete", 401);
        }

        $manager->remove($message);
        $manager->flush();

        return $this->json("message deleted", 200);
    }

    #[Route('/in/{convId}/edit/{messageId}', methods:['PUT'])]
    public function editMessage(
        #[MapEntity(id: 'convId')] PrivateConversation $privateConversation,
        #[MapEntity(id: 'messageId')] PrivateMessage $message,
        EntityManagerInterface $manager,Request $request, SerializerInterface $serializer): Response
    {
        # verif si message et conv existe

        # verif si bien auteur du message
        if($message->getAuthor() != $this->getUser()->getProfile()){
            return $this->json("not yours to change", 401);
        }

        $editedMessage =$serializer->deserialize($request->getContent(), PrivateMessage::class, 'json');
        $message->setContent($editedMessage->getContent());

        $manager->persist($message);
        $manager->flush();

        return $this->json("message edited", 200);
    }


}
