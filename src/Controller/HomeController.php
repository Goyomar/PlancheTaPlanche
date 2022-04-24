<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $miniBoutique = $doctrine->getRepository(Produit::class)->findBy([], ['created_at' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'miniBoutique' => $miniBoutique,
        ]);
    }
}
