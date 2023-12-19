<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;
use function PHPUnit\Framework\logicalAnd;

#[Route('/api/event')]
class EventController extends AbstractController
{
    #[Route('s', methods: ['GET'])]
    public function indexAll(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->json($events, 200, [], ['groups'=>'events:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function indexOne(Event $event): Response
    {
        return $this->json($event, 200, [], ['groups'=>'events:read']);
    }


    #[Route('/new', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $event->setOrganiser($this->getUser()->getProfile());
        $event->setCanceled(false);

        $json = $request->getContent();
        $data = $serializer->deserialize($json, Event::class, 'json');

        $event->setDescription($data->getDescription());
        $event->setLocation($data->getLocation());

        $status = $request->getPayload()->get('public');
        $event->setPublic($status);

        $statusLocation = $request->getPayload()->get('locationPublic');
        $event->setLocationPublic($statusLocation);

        $event->setFirstDay($data->getFirstDay());
        $event->setLastDay($data->getLastDay());


        if($event->getFirstDay() < new \DateTime()){
            return $this->json("first day can not be before today",401);
        } elseif ($event->getFirstDay() > $event->getLastDay()){ // if event can't end the same day it started put >= .
            return $this->json("last day can not be before as the start ",401);
        }

        $manager->persist($event);
        $manager->flush();

        return $this->json("new event added", 200, [], ['groups'=>'']);
    }

    #[Route('/{id}/cancel', methods: ['GET'])]
    public function cancel(Event $event, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($currentUser != $event->getOrganiser()){
            return $this->json("only the organiser can cancel the event", 200);
        }

        $event->setCanceled(true);
        $manager->persist($event);
        $manager->flush();

        return $this->json("event was successfully canceled", 200);
    }

    #[Route('/{id}/edit/days', methods:['PUT'])]
    public function editEventDays(Event $event, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager):Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($currentUser != $event->getOrganiser()){
            return $this->json("only the organiser can modify the dates");
        }

        $json = $request->getContent();
        $data= $serializer->deserialize($json, Event::class, 'json');

        $event->setFirstDay($data->getFirstDay());
        $event->setLastDay($data->getLastDay());

        if($event->getFirstDay() < new \DateTime()){
            return $this->json("first day can not be before today",401);
        } elseif ($event->getFirstDay() > $event->getLastDay()){ // if event can't end the same day it started put >=
            return $this->json("last day can not be before as the start ",401);
        }

        $manager->persist($event);
        $manager->flush();

        return $this->json("event days changed successfully", 200);


    }


}
