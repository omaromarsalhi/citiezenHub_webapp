<?php

namespace App\MyHelpers;

use App\Controller\AiResultController;
use App\Entity\AiResult;
use App\Service\AiService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AiResultServes
{
    public function addAiResult(AiResult $aiResult,EntityManagerInterface $entityManager): Response
    {
        $entityManager->persist($aiResult);
        $entityManager->flush();
        return new Response('done', Response::HTTP_CREATED);
    }


}