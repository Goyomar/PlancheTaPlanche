<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\AdresseType;
use Monolog\DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser(); // a retravailler quand le systeme de commande sera fonctionnel

        if ($doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId())) { // panier besoin de commande pour exister
            $commande = $doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId()); // 
            $paniers = $commande->getPaniers();
            dump($paniers->toArray());
        } else {
            $commande = new Commande();
            $commande->setNumero($commande->generateNumeroCommande())
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setTotal(0)
                ->setUser($user)
                ->setIsDelivered(false)
                ->setIsOrdered(false);
            $paniers = [];
        }
        
        return $this->render('shopping/index.html.twig', [
            'paniers' => $paniers,
        ]);
    }

    /**
     * @Route("/panier/add/{slug}", name="add_panier")
     */
    public function addPanier(ManagerRegistry $doctrine, Produit $produit, Request $request): Response
    {
        $em = $doctrine->getManager();
        $paniers = $this->getUser()->getPaniers(); 

        $found = false;
        if (!empty($paniers)){ // on verifie que l'utilisateur possede deja des article dans son panier
            foreach($paniers as $panier) { // si c'est le cas on va les parcourir pour savoir si le produit voulu est deja dans un panier
                if ($panier->getProduit() === $produit) { // si c'est le cas on augmente la quantite voulu de 1
                    $panier->setQte( ($panier->getQte() + 1) );
                    $found = true;
                }
            }
        }

        if(!$found){ // sinon on lui ajoute le produit receptione en recuperant sa commande actuel
            $newPanier = new Panier();
            $newPanier->setQte(1)
                      ->setUser($this->getUser())
                      ->setProduit($produit)
                      ->setCommande($doctrine->getRepository(Commande::class)->findCurrentOrder($this->getUser()->getId()));

            $em->persist($newPanier); // on valide le panier
        }
        $em->flush(); // et on le sauvegarde 
        
        return $this->redirect($request->headers->get('referer'));  // Reprend le http d'ou vient l'utilisateur et le renvoie dessus !
    }

    /**
     * @Route("/panier/delete/{id}", name="del_panier")
     */
    public function delPanier(ManagerRegistry $doctrine, Panier $panier, Request $request)
    {
        $this->getuser()->removePanier($panier);
        $doctrine->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));  // Reprend le http d'ou vient l'utilisateur et le renvoie dessus !
    }

    /**
     * @Route("/panier/plus/{slug}", name="plus_panier")
     */
    public function plusPanier(ManagerRegistry $doctrine, Produit $produit, Request $request)
    {
        $em = $doctrine->getManager();
        $paniers = $this->getUser()->getPaniers();

        foreach($paniers as $panier) { // si c'est le cas on va les parcourir pour savoir si le produit voulu est deja dans un panier
            if ($panier->getProduit() === $produit) { // si c'est le cas on augmente la quantite voulu de 1
                $panier->setQte( ($panier->getQte() + 1) );
            }
        }
        $em->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/panier/minus/{slug}", name="minus_panier")
     */
    public function minusPanier(ManagerRegistry $doctrine, Produit $produit, Request $request)
    {
        $em = $doctrine->getManager();
        $paniers = $this->getUser()->getPaniers();

        foreach($paniers as $panier) {
            if ($panier->getProduit() === $produit) {
                if($panier->getQte() > 1)
                $panier->setQte( ($panier->getQte() - 1) );
            }
        }
        $em->flush();
        return $this->redirect($request->headers->get('referer'));
    }



    /**
     * @Route("/wishlist", name="app_wishlist")
     */
    public function wishlist(): Response
    {
        return $this->render('shopping/wishlist.html.twig', [
            'wishlist' => $this->getUser()->getWishlist() // on recupere les produits qui ont ete enregistré dans la collection par l'utilisateur
        ]);
    }

    /**
     * @Route("/wishlist/add/{slug}", name="add_wishlist")
     */
    public function addWishlist(ManagerRegistry $doctrine, Produit $produit, Request $request): Response
    {
        $this->getUser()->addWishlist($produit); // on envoie le produit voulu dans la relation ManyToMany(wishlist)
        $doctrine->getManager()->flush(); // sauvegarde

        return $this->redirect($request->headers->get('referer'));  // Reprend le http d'ou vient l'utilisateur et le renvoie dessus !
    }

    /**
     * @Route("/wishlist/del/{slug}", name="del_wishlist")
     */
    public function delWishlist(ManagerRegistry $doctrine, Produit $produit, Request $request): Response
    {
        $this->getUser()->removeWishlist($produit); // on sort le produit voulu de la relation ManyToMany(wishlist)
        $doctrine->getManager()->flush(); // sauvegarde

        return $this->redirect($request->headers->get('referer'));  // Reprend le http d'ou vient l'utilisateur et le renvoie dessus !
    }

    /**
     * @Route("/order", name="order")
     */
    public function order()
    {
        $user = $this->getUser();
        $formLivraison = $this->createForm(AdresseType::class);
        $formFactu = $this->createForm(AdresseType::class);

        if ($formFactu->isSubmitted() && $formFactu->isValid()){ // a modifier en prenant en compte API PAYMENT
            dump("coucou Factu");
        }

        if ($formLivraison->isSubmitted() && $formLivraison->isValid()){// a modifier en prenant en compte API PAYMENT
            dump("coucou Livraison");
        }

        return $this->render('shopping/order.html.twig', [
            'commande' => $user->getPaniers()[0]->getCommande(),
            'paniers' => $user->getPaniers(),
            'adresses' => $user->getAdresses(),
            'formLivraison' => $formLivraison->createView(),
            'formFactu' => $formFactu->createView()
        ]);
    }
}
