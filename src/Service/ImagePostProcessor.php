<?php

namespace App\Service;

use App\Entity\PrivateConversation;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Templating\Helper\Uploaderhelper;
use Liip\Imaginebundle\imagine\Cache\CacheManager;
use App\Repository\PrivateMessageRepository;
use App\Repository\ImageRepository;

class ImagePostProcessor
{
    private UploaderHelper $uploaderHelper;
    private CacheManager $cacheManager;
    private ImageRepository $imageRepository;

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
                // return id et url (helper)
            }

        }

        return $messages;
    }


}