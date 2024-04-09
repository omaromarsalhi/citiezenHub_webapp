<?php

namespace App\MyHelpers;

use App\Entity\Product;
use App\Entity\ProductImages;
use Doctrine\ORM\EntityManagerInterface;


class ImageHelper
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public  function saveImages($files,Product $product): array
    {
        $newImagesPath=[];
        for ($i = 0; $i < sizeof($files); $i++) {
            $product_image = new ProductImages();

            $file=$files['file-' . ($i + 1)];

            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                    '../public/usersImg/',
                    $fileName
                );

            $product_image->setPath('usersImg/'.$fileName);
            $product_image->setProduct($product);

            $this->entityManager->persist($product_image);
            $this->entityManager->flush();

            $newImagesPath[]=$fileName;
        }
        return $newImagesPath;
  }



}