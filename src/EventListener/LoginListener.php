<?php

namespace App\EventListener;

use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginListener{
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        $commande = new Commande();
        $commande->setNumero($commande->generateNumeroCommande())
                 ->setCreatedAt(new \DateTimeImmutable())
                 ->setTotal(0)
                 ->setUser($user)
                 ->setIsDelivered(false)
                 ->setIsOrdered(false);
        
        $this->em->persist($commande);
        $this->em->flush();
    }
}