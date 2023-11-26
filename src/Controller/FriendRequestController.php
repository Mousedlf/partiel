<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\Friendship;
use App\Entity\PrivateConversation;
use App\Entity\Profile;
use App\Repository\FriendRequestRepository;
use App\Repository\FriendshipRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/friend')]
class FriendRequestController extends AbstractController
{
    #[Route('/requests', name: 'app_friend_request', methods: ['GET'])]
    public function index(FriendRequestRepository $friendRequestRepository): Response{

        return $this->json($friendRequestRepository->findAll(),200,[], ['groups'=> 'show_requests']); # revoir groups dans entités

    }

    #[Route('/request/received/{id}', methods: ['GET'])]
    public function indexReceivedRequests(Profile $profile): Response
    {
        if($this->getUser()->getProfile() !== $profile){
            return $this->json("none of your business", 401);
        }

        $requests = $profile->getSentFriendRequests(); # pas logique...
        return $this->json($requests,200, [],["groups"=>"show_receivedRequests"]);

    }

    #[Route('/request/send/{id}', methods: ['POST'])]
    public function sendFriendRequest(Profile $profile, EntityManagerInterface $manager, FriendshipRepository $friendshipRepository): Response
    {
        $request = new FriendRequest();

        $sentBy = $this->getUser()->getProfile();
        $sentTo = $profile;

        if ($sentBy == $sentTo) {
            return $this->json("you are sending yourself a friend request", 401);
        }

        foreach($sentBy->getFriendList() as $friend){
            if($sentTo == $friend) {
                return $this->json("already friends");
            }
        }

        # verifier si demande déja envoyée ?? why so confusing, can't seem to make in work
        foreach($sentBy->getSentFriendRequests() as $request){
            if($sentTo == $request->getToProfile()){
                return $this->json("request already sent");
            }
        }
        #verif si lui a pas deja envoyé une demande
        #return $this->json("other party already sent to a friend request");



        $request->setOfProfile($sentBy);
        $request->setToProfile($sentTo);
        $request->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($request);
        $manager->flush();

        return $this->json("friend request sent to ".$request->getToProfile()->getUsername(), 200 ); # ['groups'=> '']
    }

    #[Route('/accept/{id}', methods: ['POST'])]
    public function acceptFriendRequest(FriendRequest $request, EntityManagerInterface $manager): Response
    {
        $friendship = new Friendship();
        $personA = $request->getOfProfile();
        $personB = $request->getToProfile();

        $friendship->setFriendA($personA);
        $friendship->setFriendB($personB);
        $friendship->setCreatedAt(new \DateTimeImmutable());

        # verifier si déja dans liste d'amis ?

        # verifier si personne connectée est bien celle à qui on a envoyé la demande
        if($request->getToProfile() != $this->getUser()->getProfile()){
            return $this->json("not yours to accept", 401);
        }

        # creer conversation
        $privateConversation = new PrivateConversation();
        $privateConversation->setParticipantA($personA);
        $privateConversation->setParticipantB($personB);

        $manager->persist($friendship);
        $manager->persist($privateConversation);
        $manager->remove($request); # demande acceptée donc plus besoin de la garder
        $manager->flush();

        return $this->json($request->getOfProfile()->getUsername()."'s friend request accepted", 200,);
    }

    #[Route('/decline/{id}', name: 'decline_friend_request', methods: ['POST'])]
    public function declineFriendRequest(FriendRequest $request, EntityManagerInterface $manager): Response
    {
        # verifier si personne connectée est bien celle à qui on a envoyé la demande
        if($request->getToProfile() != $this->getUser()->getProfile()){
            return $this->json("mind your own business", 401);
        }

        $manager->remove($request);
        $manager->flush();

        return $this->json($request->getOfProfile()->getUsername()."'s friend request declined", 200 );
    }

    #[Route('/retract/{id}', methods: ['POST'])]
    public function takeBackFriendRequest(FriendRequest $request, EntityManagerInterface $manager): Response
    {
        if($request->getOfProfile() != $this->getUser()->getProfile()){
            return $this->json("mind your own business", 401);
        }

        $manager->remove($request);
        $manager->flush();

        return $this->json("it seems something made you change your mind", 200 );
    }
}
