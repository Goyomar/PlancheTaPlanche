<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$session->get("builder")) {
            $builderSession = [
                "grip" => null,
                "board" => null,
                "screws" => null,
                "truck" => null,
                "bearings" => null,
                "wheels" => null
            ];
            $session->set("builder", $builderSession);
        }

        $produits = $doctrine->getRepository(Produit::class)->findBy([], ['created_at' => 'DESC'], 3);

        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }
}
