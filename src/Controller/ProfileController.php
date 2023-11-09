<?php

namespace App\Controller;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ProfileController extends AbstractController
{
    #[Route('/profiles', name: 'app_profile')]
    public function index(ProfileRepository $repository): Response
    {
        return $this->json($repository->findAll(), 200, [],['groups'=>'show_profiles']);
    }
}
