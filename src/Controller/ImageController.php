<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Profile;
use App\Service\ImageProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ImageController extends AbstractController
{
    #[Route('/profile/{id}/image/upload', methods:['POST'] )]
    public function uploadProfilePic(Profile $profile, Request $request, ImageProcessor $imageProcessor, EntityManagerInterface $manager): Response
    {
        $image = new Image();

        $file = $request->files->get('image');
        $image->setImageFile($file);
        $image->setProfile($profile);

        $url = $imageProcessor->getImageUrl($image);

        $manager->persist($image);
        $manager->flush();

        return $this->json($url, 200);
    }
}
