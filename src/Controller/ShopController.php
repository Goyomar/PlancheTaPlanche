<?php

namespace App\Controller;

use App\Entity\Etoile;
use App\Entity\Produit;
use App\Form\SortBarType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Monolog\DateTimeImmutable;
use App\Repository\EtoileRepository;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="app_shop")
     * @Route("/shop/page/{page}", name="page_shop")
     * 
     * @param integer $page
     */
    public function index(ProduitRepository $pr, Session $session, Request $request, int $page = 1): Response
    {
        $productsPerPage = 9; // le nb de produits que l'on affiche par page
        $sortBarForm = $this->createForm(SortBarType::class);

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

        // STOCKER le resultat dans la session ou créer une session
        if (!$session->get("filter")) {
            $filterSession = [
                "categorie" => null,
                "promo" => null,
                "prixUn" => null,
                "prixDeux" => null
            ];
            $session->set("filter",$filterSession);
        } else {
            $filterSession = $session->get("filter");
        }
        $sortBarForm->handleRequest($request);
        if ($sortBarForm->isSubmitted() && $sortBarForm->isValid()){ // si l'utilisateur decide de trier les produits
            foreach ($filterSession as $key => $value) { // on gere le tableau de filtre en lui inserant les données demandé
                if ($sortBarForm->get($key)->getData() != null || $sortBarForm->get($key)->getData() === false) {
                    $filterSession[$key] = $sortBarForm->get($key)->getData(); // si il ya une donne on la rentre
                } else {
                    $filterSession[$key] = $session->set($key, null); // sinon on met la donné a null
                }
            }
            if ($filterSession['categorie'] !== null){ // si on demande une categorie on recupere l'id de celle-ci
                $filterSession['categorie'] = $filterSession['categorie']->getId();
            }
            $session->set("filter", $filterSession); // On garde les filtres en session pour la navigation
            // On cherche les produits associé a la recherche avec notre tableau
            $products = $pr->sortBarFind($filterSession["categorie"] , $filterSession['promo'], $filterSession['prixUn'], $filterSession['prixDeux'], 
                                        $productsPerPage, ($productsPerPage*$page)-$productsPerPage);
            $nbProducts = $pr->sortBarCount($filterSession["categorie"] , $filterSession['promo'], $filterSession['prixUn'], $filterSession['prixDeux'])[1];

            return $this->redirectToRoute('app_shop', []);

        } elseif ($filterSession["categorie"] !== null || $filterSession["promo"] !== null || $filterSession["prixUn"] !== null || $filterSession["prixDeux"] !== null) {
            // Si le formulaire a deja ete soumis on permet la naviguation enre les pages grace a la sauvegarde en session grace au elseif
            $products = $pr->sortBarFind($filterSession["categorie"] , $filterSession['promo'], $filterSession['prixUn'], $filterSession['prixDeux'], 
                                        $productsPerPage, ($productsPerPage*$page)-$productsPerPage);
            $nbProducts = $pr->sortBarCount($filterSession["categorie"] , $filterSession['promo'], $filterSession['prixUn'], $filterSession['prixDeux'])[1];
        
        } else {
            // chercher le nombre de produits actifs voulu a partir de la page ou l'on se trouve
            $products = $pr->findBy(['is_active' => true], ['created_at' => 'DESC'], $productsPerPage, ($productsPerPage*$page)-$productsPerPage);
            $nbProducts = $pr->count(['is_active' => true]); // On compte le nombre de produits actif dans la BDD
        }
        
        $nbPages = ceil($nbProducts / $productsPerPage); // le nombre de lien de pagination pour la vu

        dump($filterSession);
        // et créer modele carte ! et regler pb taille json
        
        return $this->render('shop/index.html.twig', [
            'sortBarForm' => $sortBarForm->createView(),
            'produits' => $products,
            'nbPages' => $nbPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/shop/{slug}", name="show_shop")
     */
    public function show(ManagerRegistry $doctrine, Produit $produit, EtoileRepository $er, Request $request): Response
    {
        $commentaire = new Commentaire();
        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);

        $vote = null;
        if($this->getUser()){

            if ($er->findVote($this->getUser()->getId(), $produit->getId())) {
                $vote = $er->findVote($this->getUser()->getId(), $produit->getId());
                dump($vote);
            }

            $commentaireForm->handleRequest($request);
            if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) { // si l'utilisateur est connecté et soumet le formulaire on genere un commentaire
                $commentaire->setUser($this->getUser());
                $commentaire->setProduit($produit);
                $commentaire->setCommentaire($commentaireForm->get('commentaire')->getData());
                $commentaire->setCreatedAt(new DateTimeImmutable('now'));
                $commentaire->setIsActive(true);

                $em = $doctrine->getManager();
                $em->persist($commentaire);
                $em->flush();
                return $this->redirectToRoute("show_shop",[
                    'slug' =>$produit->getSlug()
                ] );
            }
        }
        
        return $this->render('shop/show.html.twig', [
            'produit' => $produit,
            'commentaireForm' => $commentaireForm->createView(),
            'vote' => $vote
        ]);
    }

    /**
     * @Route("/etoile/{slug}/{note}", name="app_etoile")
     * 
     * @ParamConverter("produit", options={"mapping": {"slug" : "slug"}})
     * @ParamConverter("note")
     */
    public function etoile(ManagerRegistry $doctrine, Produit $produit, int $note)
    {
        if ($note >= 1 && $note <= 5 && $this->getUser()) {// verifie la note et que l'utilisateur et connecte
            $etoile = new Etoile(); // Alimente le vote
            $etoile->setProduit($produit);
            $etoile->setUser($this->getUser());
            $etoile->setNote($note);

            $em = $doctrine->getManager(); // envoie en bdd
            $em->persist($etoile);
            $em->flush();
        } // rajouter un else flash error

        return $this->redirectToRoute("show_shop",[
            'slug' => $produit->getSlug()
        ]);
    }

    /**
     * @Route("/etoile/edit/{slug}/{note}", name="edit_etoile")
     * 
     * @ParamConverter("produit", options={"mapping": {"slug" : "slug"}})
     * @ParamConverter("note")
     */
    public function editEtoile(ManagerRegistry $doctrine, Produit $produit, int $note)
    {
        if ($note >= 1 && $note <= 5 && $this->getUser()){
            $etoile = $doctrine->getRepository(Etoile::class)->findOneBy(['user' => $this->getUser()->getId(), 'produit' => $produit->getId()]);
            $etoile->setNote($note);
            $doctrine->getManager()->flush();
        }

        return $this->redirectToRoute("show_shop",[
            'slug' => $produit->getSlug()
        ]);
    }

    /**
     * @Route("/comment/delete/{id}", name="del_comment")
     */
    public function delComment(ManagerRegistry $doctrine, Commentaire $commentaire, Request $request)
    {
        $em = $doctrine->getManager();
        $em->remove($commentaire);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
