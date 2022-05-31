<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Adresse;
use App\Form\ResetPasswordType;
use Doctrine\Persistence\ManagerRegistry;
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
    public function index(): Response
    {
        
        return $this->render('account/index.html.twig', [
            'commandes' => $this->getUser()->getCommandes()
        ]);
    }

    /**
     * @Route("/adresse/del/{id}", name="del_adresse")
     */
    public function delAdresse(ManagerRegistry $doctrine, Adresse $adresse, Request $request): Response
    {
        $this->getUser()->removeAdress($adresse);
        $doctrine->getManager()->flush();
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

            return $this->redirectToRoute("app_account"); // on le raméne sur la page d'informations de son compte
        }

        $reset->handleRequest($request);
        if ($reset->isSubmitted() && $reset->isValid()) {
            $oldPass = $reset->get('oldPassword')->getData();
            $newPass = $reset->get('newPassword')->getData();
            if (password_verify($oldPass, $user->getPassword())) { // on verifie que l'ancien mot de passe coresponde au nouveau
                $user->setPassword( $userPasswordHasher->hashPassword($user, $newPass) ); // on hash le mot de passe et on l'associe a l'utilisateur
                $doctrine->getManager()->flush(); // on l'enregistre

                return $this->redirectToRoute("app_account");
            }
        }


        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
            'reset' => $reset->createView()
        ]);
    }
}
