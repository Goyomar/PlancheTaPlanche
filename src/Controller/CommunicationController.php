<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mail = (new Email())
                ->from($form->get('email')->getData())
                ->to(new Address('noreply@ptp.fr', 'ptp noreply'))
                ->subject($form->get('objet')->getData())
                ->text($form->get('message')->getData())
            ;

            $mailer->send($mail);
            return $this->redirectToRoute("app_home");
        }
        return $this->render('communication/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
