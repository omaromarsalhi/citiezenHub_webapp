<?php

namespace App\Controller;

use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class AdminControlllerController extends AbstractController
{
    #[Route('/admin/controlller', name: 'app_admin_controlller')]
    public function index(): Response
    {
        return $this->render('admin/customers.html.twig');
    }
    #[Route('/Show', name: 'Show')]
    public function Show(UserRepository $rep): Response
    {
        $user = $rep->findAll();
        return $this->render('admin/customers.html.twig', [
            'list' => $user,
        ]);
    }

    #[Route('/ShowDetails/{user}', name: 'Show_Details')]
    public function ShowDetailsUser($user,UserRepository $rep): Response
    {
        $user=$rep->findOneBy([ 'email' =>$user]);
        $now = new DateTime();
        $diffDays = $user->getDate()->diff($now)->days;
        $diffDays = sprintf("%d jours", $diffDays);
        $diffMonths = $user->getDate()->diff($now)->m;
        $diffyears = $user->getDate()->diff($now)->y;
        if($diffyears!==0)
        {   $diffyears = sprintf("%d years", $diffyears);
            return $this->render('admin/customer-details.html.twig',[
                'user'=>$user,
                'subscriptionPeriod' => $diffyears,
            ]);
        }
        if($diffMonths!==0)
        { $diffMonths = sprintf("%d months", $diffMonths);
            return $this->render('admin/customer-details.html.twig',[
                'user'=>$user,
                'subscriptionPeriod' => $diffMonths,
            ]);

        }

        return $this->render('admin/customer-details.html.twig',[
            'user'=>$user,
            'subscriptionPeriod' => $diffDays,
        ]);


    }
    #[Route('/Delete/{email}', name: 'Delete')]
    public function DeleteUser($email,UserRepository $rep,ManagerRegistry $doc): Response
    {
        $user=$rep->findOneBy([ 'email' =>$email]);
        $em=$doc->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('Show');
    }
    #[Route('/ShowMunicipality', name: 'ShowMunicipality')]
    public function ShowMunicipality(UserRepository $rep): Response
    {
        return $this->render('admin/add-product.html.twig');
    }


}
