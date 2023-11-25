<?php

namespace App\Controller;

use App\Entity\CommunityChat;
use App\Entity\Profile;
use App\Repository\CommunityChatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/communit')]
class CommunityChatController extends AbstractController
{
    #[Route('ies', methods:['GET'])]
    public function index(CommunityChatRepository $repository): Response
    {
        $communityChats = $repository->findAll();
        return $this->json($communityChats, 200, [], ['groups'=>'show_communities']);
    }

    #[Route('y/new', methods:['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, CommunityChatRepository $chatRepository): Response
    {
        $json = $request->getContent();
        $community = $serializer->deserialize($json, CommunityChat::class, 'json');
        $name = $community->getName();

        $communities = $chatRepository->findAll();
        foreach($communities as $communityChat){
            if($name == $communityChat->getName()){
                return $this->json("name already taken", 401);
            }
        }

        $community->setCreatedAt(new \DateTimeImmutable());
        $community->setCreatedBy($this->getUser()->getProfile());

        $manager->persist($community);
        $manager->flush();

        return $this->json("community '".$name."' created", 200);
    }

    #[Route('y/{id}/join')]
    public function joinChat(CommunityChat $community, EntityManagerInterface $manager){

        if($community->getMembers()->contains($this->getUser()->getProfile())){
            return $this->json("you are already in this community", 401);
        }

        $community->addMember($this->getUser()->getProfile());
        $manager->persist($community);
        $manager->flush();

        return $this->json("community joined", 200);

    }

    #[Route('y/{id}/leave')]
    public function leaveChat(CommunityChat $community, EntityManagerInterface $manager){

        if($community->getMembers()->contains($this->getUser()->getProfile())){
            $community->removeMember($this->getUser()->getProfile());
            $manager->persist($community);
            $manager->flush();

            return $this->json("you left the community", 401);
        }

        return $this->json("you are not part of the community, so you can not leave it", 200);

    }

    #[Route('y/{commId}/promote/admin/{profileId}')]
    public function promoteMemberToAdmin(
        #[MapEntity(id: 'commId')] CommunityChat $community,
        #[MapEntity(id: 'profileId')] Profile $profile,
        EntityManagerInterface $manager){


// lalala

    }





}
