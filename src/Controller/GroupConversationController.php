<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Repository\GroupConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/ public/conversation')]
class GroupConversationController extends AbstractController
{
    #[Route('s', methods:['GET'])]
    public function index(GroupConversationRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [],['groups'=>'']);
    }

    #[Route('/new', methods:['POST'])]
    public function createPublicConversation(EntityManagerInterface $manager): Response
    {
        $conversation = new GroupConversation();

        $conversation->setCreatedBy($this->getUser()->getProfile());
        $conversation->setAdmin($this->getUser()->getProfile());
        $conversation->addMember($this->getUser()->getProfile());

        # requete avec key "member" et value "username" ou id ?
        # verif si personne existe
        # verifier si dans liste amis $profile->getFriendList();
        # ajout profile $conversation->addMember();

        # ajout min 2 personnes
        if($conversation->getMembers() <3){
            return $this->json("two or more friends need to be added to create a group, otherwise it's a normal conversation genius");
        }

        $manager->persist($conversation);
        $manager->flush();

        return $this->json($conversation, 200, [],['groups'=>'']);
    }
}
