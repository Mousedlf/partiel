<?php

namespace App\Controller;

use App\Entity\PrivateConversation;
use App\Entity\Profile;
use App\Repository\PrivateConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/private/conversation')]
class PrivateConversationController extends AbstractController
{
    #[Route('s', methods:['GET'])]
    public function index(PrivateConversationRepository $repository): Response
    {
        $privateConversations = $repository->findAll();
        return $this->json($privateConversations, 200,[], ['groups'=>'show_privateConversations']);
    }

    #[Route('s/ofmine/{id}',  methods:['GET'])]
    public function indexAllMyConversations(Profile $profile): Response
    {
        $conversations = "faut encore ajouter la methode getPrivateConversations dans le profile";
        // $profile->getPrivateConversations();

        return $this->json($conversations, 200, [],['groups'=>'show_MyPrivateConversations'] );
    }

    #[Route('/{id}',  methods:['GET'])]
    public function indexAllMessagesOfConversation(PrivateConversation $privateConversation): Response
    {
        $current = $this->getUser()->getProfile();
        if($current == $privateConversation->getParticipantA() or $current == $privateConversation->getParticipantB()){
            $messages = $privateConversation->getPrivateMessages();
            return $this->json($messages, 200, [],['groups'=>'show_privateConversationMessages'] );
        }
        return $this->json("mind your own business", 401);

    }
}
