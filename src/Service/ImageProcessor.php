<?php

namespace App\Service;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageProcessor
{

    private UploaderHelper $helper;
    private CacheManager $cacheManager;

    public function __construct(UploaderHelper $helper, CacheManager $cacheManager){
        $this->helper = $helper;
        $this->cacheManager = $cacheManager;
    }

    public function getImageUrl($image){
        return $this->cacheManager->generateUrl($this->helper->asset($image), 'mini');
    }


}