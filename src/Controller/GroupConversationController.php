<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Entity\GroupMessage;
use App\Entity\Profile;
use App\Repository\GroupConversationRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/group/conversation')]
class GroupConversationController extends AbstractController
{
    #[Route('s', methods:['GET'])]
    public function index(GroupConversationRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [],['groups'=>'show_groupConv']);
    }

    #[Route('/{id}', methods:['GET'])]
    public function indexAllMessages(GroupConversation $groupConversation): Response
    {
        foreach ($groupConversation->getMembers() as $member){
            if($this->getUser()->getProfile() == $member){
                $messages = $groupConversation->getMessages();
                return $this->json($messages, 200,[],['groups'=>'show_privateConvMsgs']);
            }
        }

        return $this->json("you are not part of the group, sad", 401);
    }

    #[Route('/new', methods:['POST'])]
    public function createGroup(EntityManagerInterface $manager,Request $request, ProfileRepository $profileRepository): Response
    {
        $conversation = new GroupConversation();

        $conversation->setCreatedBy($this->getUser()->getProfile());
        $conversation->setAdmin($this->getUser()->getProfile());
        $conversation->addMember($this->getUser()->getProfile());
        $conversation->setCreatedAt(new \DateTimeImmutable());

        $content = $request->getContent();
        $params = json_decode($content, true); # parameters as array
        foreach($params["members"] as $potentialProfileId){

            if($potentialProfileId == $this->getUser()->getProfile()->getId()){
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
                    } elseif($profile->isPublic(false)) {
                        return $this->json("you are trying to add someone is not your friend and not public", 401);
                    } else{
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

    #[Route('/{id}/edit', methods:['POST'])]
    public function modifyGroupInfo(EntityManagerInterface $manager,Request $request, GroupConversation $conversation, SerializerInterface $serializer): Response
    {
        if($this->getUser()->getProfile() !== $conversation->getAdmin()){
            return $this->json("you are not an admin. Mind your own business");
        }

        $json = $request->getContent();
        $editedConvInfo = $serializer->deserialize($json, GroupConversation::class, 'json');
        $conversation->setName($editedConvInfo->getName());

        $manager->persist($conversation);
        $manager->flush();

        return $this->json("group info modified", 201);
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
        # $conversation->setAdmin() ; quelle personne? automatiquement 2e personne a avoir rejoint?

        foreach ($conversation->getMembers() as $member){

            if($this->getUser()->getProfile() == $member){
                $conversation->removeMember($this->getUser()->getProfile());
                $manager->flush();
                return $this->json("you went *pouf*", 200);

            }
            return $this->json("why are you trying to leave a group you are not part of to begin with ?", 401);
        }
    }

    #[Route('/{convId}/add/{profileId}', methods:['POST'])]
    public function addToGroup(
        #[MapEntity(id: 'convId')] GroupConversation $conversation,
        #[MapEntity(id: 'profileId')] Profile $profile,
        EntityManagerInterface $manager, ProfileRepository $profileRepository): Response
    {
        $currentUser = $this->getUser()->getProfile();
        if($currentUser !== $conversation->getAdmin()){
            return $this->json("you can not add people to this group. You are not privileged. sad", 401);}
        if($profile == $currentUser){
            return $this->json("you are already in the group genius", 401);
        }

        $friends = $currentUser->getFriendList();
        foreach($friends as $friend){
            if($profile == $friend or $profile->isPublic()){
                foreach($conversation->getMembers() as $member){
                    if($member == $profile){
                        return $this->json("friend or public person already in group", 401);
                    }

                    $conversation->addMember($profile);
                    $manager->persist($conversation);
                    $manager->flush();
                }
                return $this->json($profile->getUsername()." added to group", 200);
            }
        }
        return $this->json("you are trying to add someone who is not your friend or public", 401);
    }




}
