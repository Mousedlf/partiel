<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/profile')]
class ProfileController extends AbstractController
{
    #[Route('s', methods: ['GET'])]
    public function indexAllProfiles(ProfileRepository $repository): Response
    {
        $profiles = $repository->findAll();
        return $this->json($profiles, 200, [], ['groups'=>'profiles:read']);
    }
}
