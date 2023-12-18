<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Invitation;
use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

            // invitation already sent

            // autres verifs possibles

            $invitation = new Invitation();
            $invitation->setToEvent($event);
            $invitation->setToProfile($profile);

            $manager->persist($invitation);
            $manager->flush();

            return $this->json("invitation(s) sent", 200);

        }
    }


    #[Route('/invite/{inviteId}/accept')]
    public function acceptInvite(Invitation $invitation, EntityManagerInterface $manager) // PAS TESTE
    {
        $currentUser= $this->getUser()->getProfile();

        if($currentUser == $invitation->getToProfile()->getProfile())

        $event = $invitation->getToEvent();
        $event->addParticipant($currentUser);
        $startOfEvent= $event->getFirstDay();

        if($startOfEvent < new \DateTime() or $event->isCanceled()){
            $manager->remove($invitation);
            $manager->flush();
            return $this->json("event has already started or was canceled. Invite is not valid anymore and was deleted", 401);
        }

        // ajout a attendingEvents
    }

    #[Route('/invite/{inviteId}/refuse')]
    public function refuseInvite(Invitation $invitation, EntityManagerInterface $manager)
    {

    }
}
