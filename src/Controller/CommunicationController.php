<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommunicationController extends AbstractController
{
    /**
     * @Route("/faq", name="app_faq")
     */
    public function faq(): Response
    {
        return $this->render('communication/index.html.twig', []);
    }

    /**
     * @Route("/legals", name="app_legals")
     */
    public function legals(): Response
    {
        return $this->render('communication/legals.html.twig', []);
    }
    
    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(): Response
    {
        // Formulaire de contact && template mail a aenvoyer ?
        return $this->render('communication/contact.html.twig', []);
    }
}
