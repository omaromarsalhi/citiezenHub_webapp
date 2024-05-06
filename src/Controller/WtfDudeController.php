<?php

namespace App\Controller;

use App\MyHelpers\AiVerification;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

class WtfDudeController extends AbstractController
{
    #[Route('/wtf/dude', name: 'app_wtf_dude')]
    public function index(AiVerification $aiVerification): Response
    {
        $filePathFrontCin=md5('user_front'.($this->getUser()->getId()*1000+17));
        $filePathBackCin=md5('user_backCin'.($this->getUser()->getId()*1000+17));
        $aiVerification->formatJsonFilesOfCin($filePathFrontCin,$filePathBackCin);

        die();
        return new Response("done");
    }
}
