<?php

namespace App\Utils;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class ImageHelper
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public  function saveImages($file): string
    {

            $fileName = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                '../public/images/users',
                $fileName
            );

        return $fileName;
    }



}