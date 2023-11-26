<?php

namespace App\Controller;

use App\Entity\PrivateConversation;
use App\Entity\Profile;
use App\Repository\PrivateConversationRepository;
use App\Service\ImagePostProcessor;
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

    #[Route('s/{id}',  methods:['GET'])]
    public function indexAllMyConversations(Profile $profile, PrivateConversationRepository $repository): Response
    {
//        if($profile !== $this->getUser()->getProfile()){
//            return $this->json("none of your business", 401);
//        }

        $conversations = [];
        foreach($profile->getPrivateConversationIds() as $convId){
            $conversations[]= $repository->find(['id'=>$convId]);
        }

        //manque un truc, me trouve que 1ere conv

        return $this->json($conversations, 200, [],['groups'=>'show_MyPrivateConversations'] );
    }

    #[Route('/{id}',  methods:['GET'])]
    public function indexAllMessagesOfConversation(PrivateConversation $conversation, ImagePostProcessor $postProcessor): Response
    {
        $current = $this->getUser()->getProfile();
        if($current == $conversation->getParticipantA() or $current == $conversation->getParticipantB()){
            $messages = $conversation->getPrivateMessages();

            $postProcessor->putImageThumbUrlsInPrivateMessages($conversation);

            return $this->json($messages, 200, [],['groups'=>'show_privateConversationMessages'] );
        }
        return $this->json("mind your own business", 401);

    }
}
