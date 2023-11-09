<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\Friendship;
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
    #[Route('/requests', name: 'app_friend_request')]
    public function index(FriendRequestRepository $friendRequestRepository): Response{

        return $this->json($friendRequestRepository->findAll(),200,[], ['groups'=> 'show_requests']); # revoir groups dans entités

    }

    #[Route('/sendrequest/{id}', name: 'send_friend_request', methods: ['POST'])]
    public function sendFriendRequest(Profile $profile, EntityManagerInterface $manager, FriendshipRepository $friendshipRepository): Response
    {
        $request = new FriendRequest();

        $sentBy = $this->getUser()->getProfile();
        $sentTo = $profile;

        if ($sentBy == $sentTo) {
            return $this->json("you are sending yourself a friend request", 401);
        }
//        if() deja amis alors pas envoyer{
//              return $this->json("alredy friends")
//         }

        $request->setOfProfile($sentBy);
        $request->setToProfile($sentTo);

        $manager->persist($request);
        $manager->flush();

        return $this->json("friend request sent", 200 ); # revoir groups dans entités ['groups'=> '']
    }

    #[Route('/accept/{id}', name: 'accept_friend_request', methods: ['POST'])]
    public function acceptFriendRequest(FriendRequest $request, EntityManagerInterface $manager, FriendshipRepository $friendshipRepository): Response
    {
        $friendship = new Friendship();
        $friendship->setFriendA($request->getOfProfile());
        $friendship->setFriendB($request->getToProfile());

//        $exists = $friendshipRepository->find lalala
//        if ($exists){
//            return $this->json("alredy friends");
//        }

        $manager->persist($friendship);
        $manager->remove($request); # demande acceptée donc plus besoin de la garder
        $manager->flush();

        return $this->json("friend request accepted", 200 );
    }
}
