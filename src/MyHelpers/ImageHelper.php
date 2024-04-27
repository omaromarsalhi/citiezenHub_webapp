<?php

namespace App\MyHelpers;

use App\Entity\Product;
use App\Entity\ProductImages;
use App\Repository\ProductImagesRepository;
use Doctrine\ORM\EntityManagerInterface;


class ImageHelper
{
    private $entityManager;
    private $productImagesRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductImagesRepository $productImagesRepository)
    {
        $this->entityManager = $entityManager;
        $this->productImagesRepository = $productImagesRepository;
    }


    public function saveImages($files, Product $product): array
    {
        $newImagesPath = [];
        for ($i = 0; $i < sizeof($files); $i++) {
            $product_image = new ProductImages();

            $file = $files['file-' . ($i + 1)];

            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                '../public/usersImg/',
                $fileName
            );

            $product_image->setPath('usersImg/' . $fileName);
            $product_image->setProduct($product);

            $this->entityManager->persist($product_image);
            $this->entityManager->flush();

            $newImagesPath[] = $fileName;
        }
        return $newImagesPath;
    }


    public function deleteImages($imagesNotToDelete, $images): void
    {
        for ($i = 0; $i < sizeof($images); $i++) {
            if (array_search(strval($images[$i]->getIdImage()), $imagesNotToDelete) === false) {
                $this->entityManager->remove($images[$i]);
                $this->entityManager->flush();
            }
        }

    }
}