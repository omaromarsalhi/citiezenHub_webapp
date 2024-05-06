<?php

namespace App\MyHelpers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class ImageHelperUser
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
                '../public/usersImg/',
                $fileName
            );

        return $fileName;
    }



}