<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/profile')]
class ProfileController extends AbstractController
{
    #[Route('s', name: 'app_profile')]
    public function index(ProfileRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [],['groups'=>'show_profiles']);
    }

    #[Route('/{id}/visibility', name: 'profile_visibility')]
    public function changeProfileVisibility(Profile $profile, EntityManagerInterface $manager): Response
    {
        if($this->getUser()->getProfile() !== $profile){
            return $this->json("not your profile", 401);
        }
        if($profile->isPublic()){
            $profile->setPublic(false);
            $visibility = "private";
        } else {
            $profile->setPublic(true);
            $visibility = "public";
        }

        $manager->persist($profile);
        $manager->flush();

        return $this->json("visibility changed to ".$visibility, 200);
    }
}
