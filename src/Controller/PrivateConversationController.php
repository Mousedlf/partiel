<?php

namespace App\Controller;

use App\Entity\PrivateConversation;
use App\Repository\PrivateConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class PrivateConversationController extends AbstractController
{
    #[Route('/private/conversations', name: 'app_private_conversation')]
    public function index(PrivateConversationRepository $repository): Response
    {
        $privateConversations = $repository->findAll();
        return $this->json($privateConversations, 200,[], ['groups'=>'show_privateConversations']);
    }
}
