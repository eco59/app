<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    public function __construct(
        private ParameterBagInterface $params
    )
    {}

    public function square(UploadedFile $picture, ?string $folder ='', ?int $width = 250): string
    {
        //on donne un nouveau nom à l'image en MD5
        $file = md5(uniqid(rand(), true)). '.webp';

        //On récupere les informations de l'image
        $pictureInfos = getimagesize($picture);

        //format incorrect ex si c'est un pdf
        if($pictureInfos === false){
            throw new Exception('Format d\'image incorrect');
        }

        //On verifie le type mime
        switch($pictureInfos['mime']){
            case 'image/png':
                $sourcePicture = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $sourcePicture = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $sourcePicture = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image incorrect');
        }

        //on recadre l'image
        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch($imageWidth <=> $imageHeight){
            //si la largeur est plus petite que la hauteur
            case -1: //portrait
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = ($imageHeight - $imageWidth) / 2;
                break;
            case 0: //carré
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = 0;
                break;
            case 1: //paysage
                $squareSize = $imageHeight;
                $srcX = ($imageWidth - $imageHeight) / 2;
                $srcY = 0;
                break;
        }

        //On crée une nouvelle image vierge
        $resizedPicture = imagecreatetruecolor($width, $width);

        //on génère le contenu de l'image attention a être rigoureux sur ce code
        imagecopyresampled($resizedPicture, $sourcePicture, 0, 0, $srcX, $srcY, $width, $width, $squareSize, $squareSize);

        //On crée le chemin de stockage
        $path = $this->params->get('uploads_directory') . $folder;

        //On crée le dossier si il n'existe pas
        if(!file_exists($path . '/mini/')){
            mkdir($path . '/mini', 0755, true);
        }

        //On stocke l'image réduite
        imagewebp($resizedPicture, $path .'/mini/' . $width . 'x' . $width . '-' . $file);

        //on stocke l'image originale
        $picture->move($path . '/', $file);

        return $file;
    }
}