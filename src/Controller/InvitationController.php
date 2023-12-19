<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Invitation;
use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class InvitationController extends AbstractController
{
    #[Route('/event/{id}/invitations', methods: ['GET'])]
    public function index(Event $event): Response
    {
        $invitations = $event->getSentInvitations();
        return $this->json($invitations, 200, [], ['groups'=>'event-invitations:read']);
    }

    #[Route('/profile/{id}/invitations', methods: ['GET'])]
    public function indexRecievedInvites(Profile $profile): Response
    {
        if($profile == $this->getUser()->getProfile()){
            $invitations = $profile->getReceivedInvitations();
            return $this->json($invitations, 200, [], ['groups'=>'profile-invitations:read']);
        }

        return $this->json("you can only see your own invites", 401);
    }

    #[Route('/private/event/{id}/invitations', methods: ['GET'])]
    public function indexAllSentInvites(Event $event): Response
    {
        $currentUser = $this->getUser()->getProfile();
        $invites = $event->getSentInvitations();

        foreach($invites as $invite){
            if($currentUser == $invite->getToProfile() or $currentUser == $event->getOrganiser()){
                return $this->json($invites, 200, [], ['groups'=>'event-invitations:read']);

            }
        }

        return $this->json("only invited people can see the full list of invitations to the event");

    }

    #[Route('/event/{id}/invite', methods: ['POST'])]
    public function sendInvites(Event $event, Request $request, ProfileRepository $profileRepository, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($event->isPublic()){
            return $this->json("public events are for all. No invitation needed", 401);
        }

        if($currentUser != $event->getOrganiser()){
            return $this->json("public events are for all. No invitation needed", 401);
        }

        $content = $request->getContent();
        $params = json_decode($content, true); # parameters as array ; id of people you want to invite

        foreach($params["invitations"] as $potentialProfileId){

            if($potentialProfileId == $currentUser->getId()){
                return $this->json("no need to send yourself an invitation", 401);
            }

            $profile = $profileRepository->findOneBy(['id'=>$potentialProfileId]);
            if(!$profile){
                return $this->json("you are trying to invite someone who does not exist", 401);
            }

            if($event->getParticipants()->contains($potentialProfileId)){
                return $this->json("you are trying to invite someone who already attends the event", 401);
            }

            foreach($event->getSentInvitations() as $invite){
                if($invite->getToProfile()->getId() == $potentialProfileId){
                    return $this->json("you already sent ".$potentialProfileId. " an invite", 401);
                }
            }

            // autres verifs possibles

            $invitation = new Invitation();
            $invitation->setToEvent($event);
            $invitation->setToProfile($profile);

            $manager->persist($invitation);
            $manager->flush();

        }

        return $this->json("invitation(s) sent", 200);
    }


    #[Route('/invite/{id}/accept', methods: ['POST'])]
    public function acceptInvite(Invitation $invitation, EntityManagerInterface $manager)
    {
        $currentUser= $this->getUser()->getProfile();

        if($currentUser != $invitation->getToProfile()){
            return $this->json("not yours to accept", 401);
        }

        $event = $invitation->getToEvent();
        $startOfEvent= $event->getFirstDay();

        if($startOfEvent < new \DateTime() or $event->isCanceled()){
            $manager->remove($invitation);
            $manager->flush();
            return $this->json("event has already started or was canceled. Invite is not valid anymore and was deleted", 401);
        }

        $event->addParticipant($currentUser);
        $invitation->setStatus("accepted");


        $manager->persist($event);
//        $manager->remove($invitation);
        $manager->flush();

        return $this->json("invite accepted", 200);

    }

//    refuse an invite by ID
    /**
     * @param Invitation $invitation
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    #[Route('/invite/{id}/refuse')]
    public function refuseInvite(Invitation $invitation, EntityManagerInterface $manager)
    {
        $currentUser= $this->getUser()->getProfile();

        if($currentUser != $invitation->getToProfile()){
            return $this->json("not yours to refuse", 401);
        }

        $event = $invitation->getToEvent();
        $startOfEvent= $event->getFirstDay();

        if($startOfEvent < new \DateTime() or $event->isCanceled()){
            $manager->remove($invitation);
            $manager->flush();
            return $this->json("event has already started or was canceled. Invite is not valid anymore anyway and was deleted", 401);
        }

        $invitation->setStatus("refused");

        $manager->persist($event);
       // $manager->remove($invitation);
        $manager->flush();

        return $this->json("invite refused", 200);


    }






}
