<?php

namespace App\Controller;

use App\Entity\PrivateConversation;
use App\Entity\PrivateMessage;
use App\Repository\PrivateConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/private/message')]
class PrivateMessageController extends AbstractController
{
    #[Route('/', name: 'app_private_message', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('private_message/index.html.twig', [
            'controller_name' => 'PrivateMessageController',
        ]);
    }

    #[Route('/in/{id}', name: 'new_private_message', methods:['POST'])]
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

    #[Route('/delete/{id}', name: 'delete_private_message', methods:['DELETE'])]
    public function deleteMessage(EntityManagerInterface $manager, PrivateMessage $message): Response
    {
        # verif si message existe

        $manager->remove($message);
        $manager->flush();

        return $this->json("message deleted", 200);
    }
}
