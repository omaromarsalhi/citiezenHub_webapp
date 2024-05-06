<?php

namespace App\Controller;

use App\MyHelpers\NewsDataApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    #[Route('/newsGet', name: 'app_news')]
    public function index(): Response
    {
        $newsDataApi = new NewsDataApi();
        $newsList = $newsDataApi->getNews();

        return $this->render('news/index.html.twig', [
            'newsList' => $newsList,
        ]);
    }
}
