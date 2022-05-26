<?php

namespace App\Controller;

use App\Entity\Adresse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index(): Response
    {
        
        return $this->render('account/index.html.twig', [
            'commandes' => $this->getUser()->getCommandes()
        ]);
    }

    /**
     * @Route("/adresse/del/{id}", name="del_adresse")
     */
    public function delAdresse(ManagerRegistry $doctrine, Adresse $adresse, Request $request): Response
    {
        $this->getUser()->removeAdress($adresse);
        $doctrine->getManager()->flush();
        return $this->redirect($request->headers->get('referer'));
    }
}
