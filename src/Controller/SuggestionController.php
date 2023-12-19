<?php

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Event;
use App\Entity\Suggestion;
use App\Repository\ContributionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/private/event')]

class SuggestionController extends AbstractController
{
    #[Route('/{id}/suggestions', methods:['GET'])]
    public function indexAllSuggestions(Event $event): Response
    {
        $suggestions = $event->getSuggestions();
        return $this->json($suggestions, 200, [], ['groups'=>'suggestions:read']);
    }

    #[Route('/{id}/suggestion/new', methods:['POST'])]
    public function newSuggestion(Event $event, Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if(!$currentUser == $event->getOrganiser()){
            return $this->json("only the organiser can add suggestions", 401);
        }

        $json= $request->getContent();
        $data=$serializer->deserialize($json, Suggestion::class, 'json');

        $suggestion = new Suggestion();
        $suggestion->setCreatedBy($currentUser);
        $suggestion->setEvent($event);
        $suggestion->setName($data->getName());
        $suggestion->setTaken(false);

        foreach($event->getSuggestions() as $existingSuggestion){
            if($existingSuggestion->getName() == $suggestion->getName()){
                return $this->json("you already made this suggestion", 401);
            }
        }

        $manager->persist($suggestion);
        $manager->flush();

        return $this->json("your suggestion to bring ".$suggestion->getName()." was added", 200);
    }

    #[Route('/suggestion/{id}/handle', methods:['POST'])]
    public function handleSuggestion(Suggestion $suggestion, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();

        foreach($suggestion->getEvent()->getParticipants() as $participant){
            if($currentUser !=$participant or $currentUser != $suggestion->getEvent()->getOrganiser()){
                return $this->json("you are not a participant of this event. Thanks for wanting to contribute but I can not accept");
            }
        }

        if($suggestion->isTaken()){
            return $this->json($suggestion->getTakenBy()->getUsername()." is already taking care of the ".$suggestion->getName(), 401);
        }

        $suggestion->setTaken(true);
        $suggestion->setTakenBy($currentUser);

        $contribution = new Contribution();
        $contribution->setToEvent($suggestion->getEvent());
        $contribution->setName($suggestion->getName());
        $contribution->setCreatedBy($suggestion->getCreatedBy());
        $contribution->setHandledSuggestionBy($currentUser);

        $manager->persist($contribution);
        $manager->persist($suggestion);
        $manager->flush();

        return $this->json("you chose to take care of bringing the ".$suggestion->getName(), 200);
    }

    /**
     * @param Suggestion $suggestion
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ContributionRepository $contributionRepository
     * @return Response
     */
    #[Route('/suggestion/{id}/modify', methods:['PUT'])]
    public function modifySuggestion(Suggestion $suggestion, EntityManagerInterface $manager, Request $request, SerializerInterface $serializer, ContributionRepository $contributionRepository): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($currentUser != $suggestion->getCreatedBy()){
            return $this->json("only the organiser can change the suggestion", 401);
        }

        // if suggestion was taken, a contribution was created.
        // the contribution is removed when the suggestion changed.
        if($contributionRepository->findOneBy(['name'=>$suggestion->getName()])){
            $manager->remove($suggestion);
        }

        $json = $request->getContent();
        $data = $serializer->deserialize($json, Suggestion::class, 'json');

        $suggestion->setName($data->getName());
        $suggestion->setTaken(false);
        $suggestion->setTakenBy(null);


        $manager->persist($suggestion);
        $manager->flush();

        return $this->json("modified", 200);
    }



}
