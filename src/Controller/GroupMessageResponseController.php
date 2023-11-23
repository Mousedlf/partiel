<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Entity\GroupMessage;
use App\Entity\GroupMessageResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/group')]
class GroupMessageResponseController extends AbstractController
{
    #[Route('/{convId}/message/{messageId}/response', methods: ["POST"])]
    public function newResponseToGroupMessage(
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();
        $members = $conversation->getMembers();
        foreach($members as $member){

            if($currentUser = $member){
                $json = $request->getContent();
                $response = $serializer->deserialize($json,GroupMessageResponse::class , 'json');

                $response->setGroupMessage($message);
                $response->setAuthor($currentUser);
                $response->setCreatedAt(new \DateTimeImmutable());

                $manager->persist($response);
                $manager->flush();
                return $this->json("probably a fascinating comment",200);
            }

            return $this->json("you are not part of the gang", 401);
        }
    }

    #[Route('/{convId}/message/{messageId}/response/{responseId}/delete', methods: ["DELETE"])]
    public function deleteResponseToGroupMessage(
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        #[MapEntity(id: 'responseId')] GroupMessageResponse $response,
        EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();
        if($currentUser !== $response->getAuthor()){
            return $this->json("mind your own business", 401);
        }

        $manager->remove($response);
        $manager->flush();
        return $this->json("response went *pouf*",200);
    }

    #[Route('/{convId}/message/{messageId}/response/{responseId}/edit', methods: ["PUT"])]
    public function editResponseToGroupMessage(
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        #[MapEntity(id: 'messageId')] GroupMessage $message,
        #[MapEntity(id: 'responseId')] GroupMessageResponse $response,
        EntityManagerInterface $manager, Request $request, SerializerInterface $serializer): Response
    {
        $currentUser = $this->getUser()->getProfile();
        if($currentUser !== $response->getAuthor()){
            return $this->json("mind your own business", 401);
        }

        $editedResponse =$serializer->deserialize($request->getContent(), GroupMessageResponse::class, 'json');
        $response->setContent($editedResponse->getContent());

        $manager->persist($response);
        $manager->flush();
        return $this->json("response edited",200);
    }


}
