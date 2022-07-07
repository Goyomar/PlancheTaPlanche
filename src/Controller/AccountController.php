<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Adresse;
use App\Entity\Commande;
use App\Form\AdresseType;
use App\Entity\Skateboard;
use App\Form\ResetPasswordType;
use App\Repository\SkateboardRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        $planches = $doctrine->getRepository(Skateboard::class)->findBy(["user" => $user]);
        $commandes = $doctrine->getRepository(Commande::class)->findBuyedOrder($user);

        $form = $this->createForm(AdresseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adresse = new Adresse();
            $adresse->setUser($this->getUser())
                    ->setAdresse($form->get('adresse')->getData())
                    ->setVille($form->get('ville')->getData())
                    ->setCp($form->get('cp')->getData());
            $doctrine->getManager()->persist($adresse);
            $doctrine->getManager()->flush();
            $this->addFlash('sucess','Votre adresse a bien été mis a jour !');
        }
        dump($commandes);
        
        return $this->render('account/index.html.twig', [
            'commandes' => $commandes,
            'planches' => $planches,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/adresse/del/{id}", name="del_adresse")
     */
    public function delAdresse(ManagerRegistry $doctrine, Adresse $adresse, Request $request): Response
    {
        $this->getUser()->removeAdress($adresse);
        $doctrine->getManager()->flush();
        $this->addFlash('sucess','Votre adresse a bien été supprimé !');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/skateboard/del/{id}", name="del_skateboard")
     */
    public function delSkateboard(ManagerRegistry $doctrine, Skateboard $skateboard, Request $request)
    {
        $em = $doctrine->getManager();
        $em->remove($skateboard);
        $em->flush();
        $this->addFlash('sucess','Votre skateboard a bien été supprimé !');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/account/edit", name="edit_account")
     */
    public function editAccount(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class,$user); // instancie le formulaire pour le changement des infos persos
        $reset = $this->createForm(ResetPasswordType::class); // instancie le formulaire pour le changement de mot de passe

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNom($form->get('nom')->getData())
                 ->setPrenom($form->get('prenom')->getData())
                 ->setEmail($form->get('email')->getData()); // on modifie les informations de l'utilisateur avec la methode associé
            $doctrine->getManager()->flush(); // on applique les modifs
            $this->addFlash('sucess','Vos informations ont bien été mis a jour !');

            return $this->redirectToRoute("app_account"); // on le raméne sur la page d'informations de son compte
        }

        $reset->handleRequest($request);
        if ($reset->isSubmitted() && $reset->isValid()) {
            $oldPass = $reset->get('oldPassword')->getData();
            $newPass = $reset->get('newPassword')->getData();
            if (password_verify($oldPass, $user->getPassword())) { // on verifie que l'ancien mot de passe coresponde au nouveau
                $user->setPassword( $userPasswordHasher->hashPassword($user, $newPass) ); // on hash le mot de passe et on l'associe a l'utilisateur
                $doctrine->getManager()->flush(); // on l'enregistre
                $this->addFlash('sucess','Votre mot de passe a bien été mis a jour !');

                return $this->redirectToRoute("app_account");
            }
        }


        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
            'reset' => $reset->createView()
        ]);
    }

    /**
     * @Route("/account/delete", name="delete_account")
     */
    public function deleteAcount(ManagerRegistry $doctrine)
    {
        $user = $this->getUser();
        $em = $doctrine->getManager();
        $commandes = $user->getCommandes();
        $nbCommande = count($commandes);

        if ($nbCommande === 1) {
            $em->remove($commandes[0]);
        } else {
            $em->remove($commandes[$nbCommande-1]);
        }

        $newSession = new Session();
        $newSession->invalidate();
        
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("app_home");
    }
}
