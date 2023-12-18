<?php

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/private/event')]
class ContributionController extends AbstractController
{
    #[Route('/{id}/contributions', methods:['GET'])]
    public function indexAllContributionsAndSuggestions(): Response
    {

    }


    #[Route('/{id}/contribution/add', methods: ['PUT'])]
    public function addContribution(SerializerInterface $serializer, Request $request, Event $event, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        // si invité et invitation acceptée

        if(!$event->isPublic() && !$event->isLocationPublic()){

            $contribution = new Contribution();

            $json = $request->getContent();
            $data = $serializer->deserialize($json, Contribution::class, 'json');

            $contribution->setName($data->getName());
            $contribution->setToEvent($event);
            $contribution->setCreatedBy($currentUser);

            $manager->persist($contribution);
            $manager->flush();

            return $this->json("thanks for the ".$contribution->getName(), 200);

        }

        return $this->json("contributions can only be made for private house events", 401);


    }


}
