<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Skateboard;
use App\Form\SkateboardType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuilderController extends AbstractController
{
    /**
     * @Route("/builder", name="app_builder")
     */
    public function index(Session $session, Request $request, ManagerRegistry $doctrine): Response
    {
        // STOCKER le setup dans la session ou créer une session
        if (!$session->get("builder")) {
            $builderSession = [
                "grip" => null,
                "board" => null,
                "screws" => null,
                "truck" => null,
                "bearings" => null,
                "wheels" => null
            ];
            $session->set("builder",$builderSession);
        } else {
            $builderSession = $session->get("builder");
        }
        $skateboard = new Skateboard();
        $form = $this->createForm(SkateboardType::class, $skateboard);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){ // a test
            if ($this->getUser()) {
                if($builderSession["grip"]->getCategorie() === "grip" && $builderSession["board"]->getCategorie() === "board" &&
                    $builderSession["screws"]->getCategorie() === "screws" && $builderSession["truck"]->getCategorie() === "truck" &&
                    $builderSession["bearings"]->getCategorie() === "bearings" && $builderSession["wheels"]->getCategorie() === "wheels") {
                    $skateboard->setCreatedAt(new \DateTime()) // si le builder est pleinement remplie et que l'utilisateur est connecté
                               ->setUser($this->getUser()) // j'alimente l'objet avec les infos manquantes
                               ->addComposer($builderSession["grip"]) // j'alimente le manytomany avec chaque objet
                               ->addComposer($builderSession["board"])
                               ->addComposer($builderSession["screws"])
                               ->addComposer($builderSession["truck"])
                               ->addComposer($builderSession["bearings"])
                               ->addComposer($builderSession["wheels"]);
                    $em = $doctrine->getManager();
                    $em->persist($skateboard); // je sauvegarde
                    $em->flush();
                    $builderSession = [ // je reset le builder
                        "grip" => null,
                        "board" => null,
                        "screws" => null,
                        "truck" => null,
                        "bearings" => null,
                        "wheels" => null
                    ];
                    $session->set("builder",$builderSession);
                    // return la vue de la planche sur le site ?
                }
            }
            
        }
        
        dump($builderSession);
        return $this->render('builder/index.html.twig', [
            'form' => $form->createView(),
            'builder' => $builderSession
        ]);
    }

    /**
     * @Route("/builder/redirect/{idCategorie}", name="redirect_builder")
     */
    public function redirectBuilder(int $idCategorie, Session $session)
    {
        if ($idCategorie === 3 || $idCategorie === 1 || $idCategorie === 5 ||  // marche pas
            $idCategorie === 4 || $idCategorie === 6 || $idCategorie === 2) {
            $filterSession = [
                "categorie" => $idCategorie,
                "promo" => null,
                "prixUn" => null,
                "prixDeux" => null
            ];
        } else {
            $filterSession = [
                "categorie" => null,
                "promo" => null,
                "prixUn" => null,
                "prixDeux" => null
            ];
        }
        
        $session->set("filter",$filterSession);
        return $this->redirectToRoute("app_shop");
    }

    /**
     * @Route("/builder/add/{slug}", name="add_builder")
     */
    public function addBuilder(Produit $product, Session $session)
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
        } else {
            $builderSession = $session->get("builder");
        }
        $builderSession[$product->getCategorie()->getNom()] = $product;
        $session->set("builder", $builderSession);

        return $this->redirectToRoute("app_builder");
    }

    /**
     * @Route("/builder/del/{slug}", name="del_builder")
     */
    public function delBuilder(Produit $product, Session $session, Request $request)
    {
        $builderSession = $session->get("builder");
        $builderSession[$product->getCategorie()->getNom()] = null; // je sors le produit du builder
        $session->set("builder", $builderSession);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/builder/transform", name="transform_session")
     */
}

// function transformer en panier, builder en session always un nouveau quand save reset session, quand save avoir son setup dans mon compte, 
// redirect vers les setups , details setup



// envoye du tout et reset la session
// vue liste des skates et détails
// btn transform skate to cart
// dans my account accéder a ses planches et pouvoir les edits