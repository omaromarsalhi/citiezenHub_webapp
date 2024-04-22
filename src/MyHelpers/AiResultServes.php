<?php

namespace App\MyHelpers;




use App\Entity\AiResult;
use Doctrine\ORM\EntityManagerInterface;

class AiResultServes
{

    public function addAiResult(AiResult $aiResult,EntityManagerInterface $entityManager):void
    {
        $entityManager->persist($aiResult);
        $entityManager->flush();
    }

}