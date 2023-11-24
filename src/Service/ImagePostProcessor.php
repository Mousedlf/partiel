<?php

namespace App\Service;

use App\Entity\PrivateConversation;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ImageRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImagePostProcessor
{
    private UploaderHelper $helper;
    private CacheManager $cacheManager;
    private ImageRepository $imageRepository;

    public function __construct(UploaderHelper $helper, CacheManager $cacheManager,ImageRepository $imageRepository){
        $this->helper = $helper;
        $this->cacheManager = $cacheManager;
        $this->imageRepository = $imageRepository;
    }


    public function getImagesAssociatedToGivenIds($ids){
        $images = [];
        foreach ($ids as $id){
            $image = $this->imageRepository->findOneBy(['id'=>$id]);
            if($image){
                $images[]= $image;
            }
        }
        return $images;
    }

    public function putImageThumbUrlsInPrivateMessages(PrivateConversation $conversation){
        $messages = $conversation->getPrivateMessages();

        foreach ($messages as $message){
            $images = $message->getImages();
            $imageUrls = new ArrayCollection();

            foreach ($images as $image){
                $imageUrl =[
                    "id"=>$image->getId(),
                    "url"=>$this->cacheManager->generateUrl($this->helper->asset($image), 'mini')
                ];
                $imageUrls[] = $imageUrl;
            }
            $message->setImageUrls($imageUrls);
        }
        return $messages;
    }

    public function getImageThumbUrl($image){

        $imageUrl = $this->cacheManager->generateUrl($this->helper->asset($image), 'mini');
        return $imageUrl;
    }




}