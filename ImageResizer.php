<?php

class ImageResizer
{
    private $availableResolutions;
    private $images = [];

    public function __construct(string $inputName, array $resolutions = [1536, 1344, 1152, 960, 768, 576, 384, 192], array $allowedTypes = ["image/jpeg", "image/png"])
    {
        if(!empty(array_filter($_FILES[$inputName]['name']))) {
            $tmpImg = $_FILES[$inputName];
            
            foreach($tmpImg["tmp_name"] as $key => $value){
                $this->images[$key]["tmp_name"] = $value;
                $this->images[$key]["name"] = $tmpImg["name"][$key];
                $this->images[$key]["type"] = $tmpImg["type"][$key];
            }    

        } else {
            throw new Exception('Error with uploading image or with the inputName used');
        }
        
        $this->availableResolutions = $resolutions;
        $this->allowedTypes = $allowedTypes;
    }
    
    private function optimizeImage($image, $resolution){
        $createdImage= imagecreatefromjpeg($image['tmp_name']);
        $imageWidth = imagesx($createdImage);
        $imageHeight = imagesy($createdImage);

        $ratio = $resolution / $imageWidth;
        $newHeight = $imageHeight * $ratio;

        $newImg = imagecreatetruecolor($resolution, $newHeight);
        imagecopyresampled($newImg, $createdImage, 0, 0, 0, 0, $resolution, $newHeight, $imageWidth, $imageHeight);
        
        return $newImg;
    }

    public function storeImages(string $path, int $quality = 30){
        foreach($this->images as $image) {
            if(in_array($image["type"], $this->allowedTypes)) {
                foreach($this->availableResolutions as $resolution) {
                    $optimizedImage = $this->optimizeImage($image, $resolution);

                    $imageFullPath = $path . "\\" . $image["name"] . "_" . $resolution;
                    switch ($image["type"]) {
                        case 'image/jpeg':
                            imagejpeg($optimizedImage , $imageFullPath . ".jpg",  $quality);
                            echo  $imageFullPath . ".jpg";
                            break;
                        case 'image/png':
                            imagepng($optimizedImage, $imageFullPath . ".png", $quality);
                            echo  $imageFullPath . ".png";
                            break;
                        default:
                            break;
                    }
                }               
            }
        }
    }
}
