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
    public function indexAllContributions(Event $event): Response
    {
        $contributions = $event->getContributions();
        return $this->json($contributions, 200, [], ['groups'=>'contributions:read']);
    }


    #[Route('/{id}/contribution/add', methods: ['POST'])]
    public function addContribution(SerializerInterface $serializer, Request $request, Event $event, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if(!$event->getParticipants()->contains($currentUser)){
            return $this->json("you are not a participant of this event. Thanks for wanting to contribute but I can not accept");
        }

        if(!$event->isPublic() && !$event->isLocationPublic()){

            $contribution = new Contribution();

            $json = $request->getContent();
            $data = $serializer->deserialize($json, Contribution::class, 'json');

            $contribution->setName($data->getName());
            $contribution->setToEvent($event);
            $contribution->setCreatedBy($currentUser);
            $contribution->setQuantity($data->getQuantity());

            $manager->persist($contribution);
            $manager->flush();

            return $this->json("thanks for the ".$contribution->getName(), 200);

        }

        return $this->json("contributions can only be made for private house events", 401);

    }

    #[Route('/contribution/{id}/remove', methods: ['DELETE'])]
    public function removeContribution(Contribution $contribution, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($contribution->getToEvent()->getOrganiser() != $currentUser or $contribution->getCreatedBy() != $currentUser){
            return $this->json("only the organiser or the one who created the contribution can remove it");
        }

        $manager->remove($contribution);
        $manager->flush();

        return $this->json("contribution was removed", 401);

    }


}
