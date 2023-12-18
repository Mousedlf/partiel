<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ParticipantController extends AbstractController
{
    #[Route('/event/{id}/participants', methods: ['GET'])]
    public function indexAllParticipantsOfEvent(Event $event): Response
    {
        $participants = $event->getParticipants();
        return $this->json($participants, 200, [], ['groups'=>'event-participants:read']);
    }

    #[Route('/events/profile/{id}', methods: ['GET'])]
    public function indexAllYourComingEvents(Profile $profile): Response //Agenda
    {
        $events = $profile->getAttendingEvents();
        return $this->json($events, 200, [], ['groups'=>'event-attending:read']);
    }

    #[Route('/event/{id}/attend', methods: ['POST'])]
    public function attendPublicEvent(Event $event, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($event->getParticipants()->contains($currentUser)){
            return $this->json("you are already listed as a participant",200);
        }

        if($event->isCanceled()){
            return $this->json("sorry but event was canceled",200);
        }

        if($event->isPublic()){
            $event->addParticipant($currentUser);
            $manager->persist($event);
            $manager->flush();

            return $this->json("you are now listed as a participant",200);
        }

        return $this->json("this event is private. you need to be invited",401);

    }
}
