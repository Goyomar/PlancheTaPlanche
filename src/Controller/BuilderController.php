<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Skateboard;
use App\Form\SkateboardType;
use Monolog\DateTimeImmutable;
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
                if($builderSession["grip"]->getCategorie()->getNom() === "grip" && $builderSession["board"]->getCategorie()->getNom() === "board" &&
                    $builderSession["screws"]->getCategorie()->getNom() === "screws" && $builderSession["truck"]->getCategorie()->getNom() === "truck" &&
                    $builderSession["bearings"]->getCategorie()->getNom() === "bearings" && $builderSession["wheels"]->getCategorie()->getNom() === "wheels") {
                    $em = $doctrine->getManager();
                    
                    $skateboard->setCreatedAt(new \DateTimeImmutable()) // si le builder est pleinement remplie et que l'utilisateur est connecté
                               ->setUser($this->getUser()); // j'alimente l'objet avec les infos manquantes                                                           

                               $grip = $doctrine->getRepository(Produit::class)->find($builderSession["grip"]->getId());
                               $board = $doctrine->getRepository(Produit::class)->find($builderSession["board"]->getId());
                               $screws = $doctrine->getRepository(Produit::class)->find($builderSession["screws"]->getId());
                               $bearings = $doctrine->getRepository(Produit::class)->find($builderSession["bearings"]->getId());
                               $wheels = $doctrine->getRepository(Produit::class)->find($builderSession["wheels"]->getId());
                               $truck = $doctrine->getRepository(Produit::class)->find($builderSession["truck"]->getId());

                            $skateboard->addComposer($grip)
                                ->addComposer($board)
                                ->addComposer($screws)
                                ->addComposer($bearings)
                                ->addComposer($wheels)
                                ->addComposer($truck);
                               

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
                    $this->addFlash('sucess','Votre planche a bien été enregistré !');
                    return $this->redirectToRoute("show_builder", ["skateboard" => $skateboard->getId()]);
                } else {
                    $this->addFlash('error','Tout les éléments doivent être présent pour enregistrer la planche !');
                }
            } else {
                $this->addFlash('error','Vous devez être connecté pour enregistrer une planche !');
            }
            
        }
        
        dump($builderSession);
        return $this->render('builder/index.html.twig', [
            'form' => $form->createView(),
            'builder' => $builderSession
        ]);
    }

    /**
     * @Route("/builder/show/{skateboard}", name="show_builder")
     */
    public function showBuilder(Skateboard $skateboard)
    {
        return $this->render('builder/show.html.twig', [
            'skateboard' => $skateboard
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
        $this->addFlash('sucess','Votre produit a bien été ajouté au builder !');

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
        $this->addFlash('sucess','Votre produit a bien été suppprimé du builder !');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/builder/transform", name="transform_builder")
     */
    public function transformBuilder(ManagerRegistry $doctrine, Session $session)
    {
        if ($this->getUser()) { // le panier n'est accessible qu'au utilisateur
            $em = $doctrine->getManager();
            $user = $this->getUser();
            $commande = $doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId()); // reucpere la commande actuelle
            $paniers = $commande->getPaniers();

            $builderSession = $session->get("builder"); 
            
            $builderToProduit = [];
            if($builderSession["grip"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["grip"]->getId());}
            if($builderSession["board"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["board"]->getId());}
            if($builderSession["screws"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["screws"]->getId());}
            if($builderSession["bearings"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["bearings"]->getId());}
            if($builderSession["wheels"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["wheels"]->getId());}
            if($builderSession["truck"] != null) {$builderToProduit []= $doctrine->getRepository(Produit::class)->find($builderSession["truck"]->getId());}
            

            foreach ($builderToProduit as $produit) {
                $found = false;
                if (!empty($paniers)){ // on verifie que l'utilisateur possede deja des article dans son panier
                    foreach($paniers as $panier) { // si c'est le cas on va les parcourir pour savoir si le produit voulu est deja dans un panier
                        if ($panier->getProduit() === $produit) { // si c'est le cas on augmente la quantite voulu de 1
                            $panier->setQte( ($panier->getQte() + 1) );
                            $found = true;
                        }
                    }
                }
                if($found == false){ // sinon on lui ajoute le produit receptione en recuperant sa commande actuel
                    $newPanier = new Panier();
                    $newPanier->setQte(1)
                            ->setUser($user)
                            ->setProduit($produit)
                            ->setCommande($commande);

                    $em->persist($newPanier); // on valide le panier
                }
            }    
            $em->flush(); // et on les sauvegarde
            $this->addFlash('sucess','Vos produits ont bien été ajouté au panier !');
            
            return $this->redirectToRoute("app_panier");
        } else {
            $this->addFlash('error','Vous devez être connecté pour ajouter ces produits aux panier !');
            return $this->redirectToRoute("app_login");
        }
    }


    /**
     * @Route("/builder/reset", name="reset_builder")
     */
    public function resetBuilder(Request $request, Session $session)
    {
        $builderSession = [
            "grip" => null,
            "board" => null,
            "screws" => null,
            "truck" => null,
            "bearings" => null,
            "wheels" => null
        ];
        $session->set("builder", $builderSession);

        $this->addFlash('sucess','Votre builder a bien été réinitialiser !');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/builder/skateboard/transform/{skateboard}", name="transform_skateboard")
     */
    public function transformSkateboard(ManagerRegistry $doctrine, Skateboard $skateboard)
    {
        if ($this->getUser()) { // le panier n'est accessible qu'au utilisateur
            $em = $doctrine->getManager();
            $user = $this->getUser();
            $commande = $doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId()); // reucpere la commande actuelle
            $paniers = $commande->getPaniers();

            $builderToProduit = $skateboard->getComposer();

            foreach ($builderToProduit as $produit) {
                $found = false;
                if (!empty($paniers)){ // on verifie que l'utilisateur possede deja des article dans son panier
                    foreach($paniers as $panier) { // si c'est le cas on va les parcourir pour savoir si le produit voulu est deja dans un panier
                        if ($panier->getProduit() === $produit) { // si c'est le cas on augmente la quantite voulu de 1
                            $panier->setQte( ($panier->getQte() + 1) );
                            $found = true;
                        }
                    }
                }
                if($found == false){ // sinon on lui ajoute le produit receptione en recuperant sa commande actuel
                    $newPanier = new Panier();
                    $newPanier->setQte(1)
                            ->setUser($user)
                            ->setProduit($produit)
                            ->setCommande($commande);

                    $em->persist($newPanier); // on valide le panier
                }
            }    
            $em->flush(); // et on les sauvegarde 
            
            $this->addFlash('sucess','Vos produits ont bien été ajouté au panier !');
            return $this->redirectToRoute("app_panier");
        } else {
            $this->addFlash('error','Vous devez être connecté pour ajouter ces produits aux panier !');
            return $this->redirectToRoute("app_login");
        }
    }
}