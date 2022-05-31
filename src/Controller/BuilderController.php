<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuilderController extends AbstractController
{
    /**
     * @Route("/builder", name="app_builder")
     */
    public function index(): Response
    {
        return $this->render('builder/index.html.twig', [
            'controller_name' => 'BuilderController',
        ]);
    }
}
