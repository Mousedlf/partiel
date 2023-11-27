<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\PrivateConversation;
use App\Service\ImagePostProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api')]
class ImageController extends AbstractController
{
    #[Route('/upload/image', methods: ['POST'])]
    public function uploadImage(Request $request, EntityManagerInterface $manager, ImagePostProcessor $postProcessor): Response
    {
       $image = new Image();

       $file = $request->files->get('image');

       $image->setImageFile($file);
       $image->setUploadedBy($this->getUser()->getProfile());

       $manager->persist($image);
       $manager->flush();

       $url = $postProcessor->getImageThumbUrl($image);

       $response = [
           "id" => $image->getId(),
           "message"=>"image uploaded and ready to be associated with a message",
           "url"=>$url
       ];


       return $this->json($response, 200);
    }
}
