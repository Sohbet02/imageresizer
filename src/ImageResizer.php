<?php
namespace SGUpgrader\Image;

class ImageResizer
{
    private static $allowedTypes = ["jpg", "png", "jpeg"];
    
    /**
     * @param $imgPath (image path)
     * @param $resolution (new image width in px)
     */
    private static function resizeImage($imgPath, $resolution){

        $createdImage= imagecreatefromjpeg($imgPath);
        $imageWidth = imagesx($createdImage);
        $imageHeight = imagesy($createdImage);

        $ratio = $resolution / $imageWidth;
        $newHeight = $imageHeight * $ratio;

        $newImg = imagecreatetruecolor($resolution, $newHeight);
        imagecopyresampled($newImg, $createdImage, 0, 0, 0, 0, $resolution, $newHeight, $imageWidth, $imageHeight);
        
        return $newImg;

    }

    /**
     * @param $path (array of file paths of the images)
     * @param optional $resolutions (array of width in px for resizing and saving image)
     */
    public static function resize(array $imgPaths, string $path = null, array $resolutions = [1536, 1344, 1152, 960, 768, 576, 384, 192], int $quality = 30) 
    {
        foreach($imgPaths as $imgPath) {
            if(file_exists($imgPath)) {

                $pathinfo = pathinfo($imgPath);
                $extension = $pathinfo['extension'];
                $path = $path ? $path : $pathinfo['dirname'];

                if(in_array($extension, self::$allowedTypes)) {
                    foreach($resolutions as $resolution) {

                        $optimizedImage = self::resizeImage($imgPath, $resolution);
                        
                        $imgSavePath = $path . "\\" . $pathinfo['filename'] . "_" . $resolution;
                        switch ($extension) {

                            case 'jpg':
                                imagejpeg($optimizedImage , $imgSavePath . ".jpg",  $quality);
                                break;
                            
                            case 'jpeg':
                                imagejpeg($optimizedImage , $imgSavePath . ".jpeg",  $quality);
                                break;

                            case 'png':
                                imagepng($optimizedImage, $imgSavePath . ".png", $quality);
                                break;

                            default:
                                break;

                        }

                    }
                } else {
                    throw new Exception('Only jpg and png types are allowed to resize with ImageResizer');
                }

            } else {
                throw new Exception('Couldn\' resolve file path: ' . $imgPath);
            }
        }
    }
}
