<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
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
        $user = $this->getUser();

        if ($doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId())) { // panier besoin de commande pour exister
            $commande = $doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId());
            $paniers = $commande->getPaniers()->toArray();
        } else {
            $commande = new Commande();
            $commande->setNumero($commande->generateNumeroCommande())
                ->setCreatedAt(new DateTimeImmutable('now'))
                ->setTotal(0)
                ->setUser($user)
                ->setIsDelivered(false);
        }
        dump($paniers);

        return $this->render('shopping/index.html.twig', [
            'paniers' => $paniers,
        ]);
    }

    /**
     * @Route("/wishlist", name="app_wishlist")
     */
    public function wishlist(): Response
    {
        return $this->render('shopping/wishlist.html.twig', [
            'wishlist' => $this->getUser()->getWishlist()
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
}
