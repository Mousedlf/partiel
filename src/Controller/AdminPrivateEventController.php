<?php

namespace App\Controller;

use App\Entity\AdminPrivateEvent;
use App\Entity\Event;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/private/event')]
class AdminPrivateEventController extends AbstractController
{
    #[Route('/{id}/admins', methods:['GET'])]
    public function indexAdminsOfEvent(Event $event): Response
    {
        $admins = $event->getAdmins();
        return $this->json($admins, 200, [], ['groups'=>'event-admins:read']);
    }

    #[Route('/{id}/admin/promote', methods:['POST'])]
    public function promoteAdmins(Event $event,Request $request, EntityManagerInterface $manager, ProfileRepository $profileRepository): Response
    {
        $currentUser = $this->getUser()->getProfile();

        if($currentUser != $event->getOrganiser()){
            return $this->json("only the organiser can promote", 401);
        }

        // verif si bien organiser

        $content = $request->getContent();
        $params = json_decode($content, true); # parameters as array ; ids of people you wish to promote

        foreach($params["admins"] as $potentialProfileId){

            $profile = $profileRepository->findOneBy(['id'=>$potentialProfileId]);

            if($potentialProfileId == $currentUser->getId()){
                return $this->json("you are the organiser and thus superadmin", 401);
            }

            if(!$event->getParticipants()->contains($profile)){
                return $this->json("you can only promote people who are participants and ".$profile->getId()." is not" , 401);

            }

            $admin = new AdminPrivateEvent();
            $admin->setProfile($profile);
            $admin->setEvent($event);

            $manager->persist($admin);
        }

        $manager->flush();

        return $this->json("promotions ok", 200);
    }

    #[Route('/admin/{id}/demote', methods:['POST'])]
    public function demoteAdmin(AdminPrivateEvent $admin, EntityManagerInterface $manager): Response
    {
        $currentUser = $this->getUser()->getProfile();
        $event = $admin->getEvent();

        if ($currentUser != $event->getOrganiser()) {
            return $this->json("only the organiser can demote", 401);
        }

        $manager->remove($admin);
        $manager->flush();

        return $this->json($admin->getProfile()->getUsername()." was demoted to a simple participant", 200);

    }






}
