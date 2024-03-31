<?php

namespace App\Controller;

use App\Entity\Reeclamation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationshController extends AbstractController
{
    #[Route('/', name: 'app_reclamationsh')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recc = $entityManager->getRepository(Reeclamation::class)->findBy([], ['createdAt' => 'DESC']);
        $last_reclamations = [];

        foreach ($recc as $reclamation) {
            $subject = $reclamation->getSubject();

            if (!isset($rec[$subject]) || $rec[$subject]->getCreatedAt() < $reclamation->getCreatedAt()) {
                $rec[$subject] = $reclamation;
            }
        }

        return $this->render('reclamationsh/index.html.twig', [
            'controller_name' => 'ReclamationshController',
            'rec' => $rec
        ]);
    }
}
