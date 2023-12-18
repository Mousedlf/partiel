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

#[Route('/api/event')]
class EventController extends AbstractController
{

    #[Route('s', methods: ['GET'])]
    public function indexAll(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->json($events, 200, [], ['groups'=>'events:read']);
    }


    #[Route('/new', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $event->setOrganiser($this->getUser()->getProfile());

        $json = $request->getContent();
        $data = $serializer->deserialize($json, Event::class, 'json');

        $event->setDescription($data->getDescription());
        $event->setLocation($data->getLocation());

        $status = $request->getPayload()->get('public');
        $event->setPublic($status);

        $statusLocation = $request->getPayload()->get('locationPublic');
        $event->setLocationPublic($statusLocation);

        $manager->persist($event);
        $manager->flush();

        return $this->json("new event added", 200, [], ['groups'=>'']);
    }
}
