<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Repository\GroupConversationRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/group/conversation')]
class GroupConversationController extends AbstractController
{
    #[Route('s', methods:['GET'])]
    public function index(GroupConversationRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [],['groups'=>'show_groupConv']);
    }

    #[Route('/new', methods:['POST'])]
    public function createGroup(EntityManagerInterface $manager,Request $request, ProfileRepository $profileRepository): Response
    {
        $conversation = new GroupConversation();

        $conversation->setCreatedBy($this->getUser()->getProfile());
        $conversation->setAdmin($this->getUser()->getProfile());
        $conversation->addMember($this->getUser()->getProfile());

        # requete || "members": ['1','2','5']
        $content = $request->getContent();
        $params = json_decode($content, true); # parameters as array
        foreach($params["members"] as $potentialProfileId){

            if($$potentialProfileId == $this->getUser()->getProfile()->getId()){
                return $this->json("you are automatically included in the group", 401);
            }

            $profile = $profileRepository->findOneBy(['id'=>$potentialProfileId]);
            if(!$profile){
                return $this->json("you are trying to add someone who does not exist", 401);
            }

            $friends = $this->getUser()->getProfile()->getFriendList();
                foreach($friends as $friend){
                    if($profile = $friend){
                        $conversation->addMember($profile);
                    }else{
                        return $this->json("you are trying to add someone who is not your friend", 401);
                    }
                }
            }

        if(count($conversation->getMembers()) < 2){
            return $this->json("two or more friends need to be added to create a group, otherwise it is a normal conversation genius");
        }

        $manager->persist($conversation);
        $manager->flush();

        return $this->json("new group created", 200);
    }

    #[Route('/delete/{id}', methods:['DELETE'])]
    public function deleteGroup(GroupConversation $conversation, EntityManagerInterface $manager): Response
    {
        if($this->getUser()->getProfile() !== $conversation->getAdmin()){
            return $this->json("you are not the admin, you can not delete the group. Just leave", 401);
        }

        $manager->remove($conversation);
        $manager->flush();

        return $this->json("group chat went *pouf*", 200);
    }

    #[Route('/leave/{id}', methods:['POST'])]
    public function leaveGroup(GroupConversation $conversation, EntityManagerInterface $manager): Response
    {

        # si l'admin quitte faire qqchose

        foreach ($conversation->getMembers() as $member){

            if($this->getUser()->getProfile() == $member){
                $conversation->removeMember($this->getUser()->getProfile());
                $manager->flush();
                return $this->json("you went *pouf*", 200);

            }
            return $this->json("why are you trying to leave a group you are not part of to begin with ?", 401);
        }

    }

}
