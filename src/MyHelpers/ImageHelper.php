<?php

namespace App\MyHelpers;

use App\Entity\Product;
use App\Entity\ProductImages;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;


class ImageHelper
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public  function saveImages(Mixed $file,Product $product): void{
        if ($file) {
            $product_image = new ProductImages();
            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                    '../public/usersImg/',
                    $fileName
                );
            $product_image->setPath('usersImg/'.$fileName);
            $product_image->setProduct($product);

            $this->entityManager->persist($product_image);
            $this->entityManager->flush();
        }
  }
}