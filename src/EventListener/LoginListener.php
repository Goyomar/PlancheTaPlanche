<?php

namespace App\EventListener;

use App\Entity\Commande;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginListener{
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$this->doctrine->getRepository(Commande::class)->findCurrentOrder($user->getId())){
            $commande = new Commande();
            $commande->setNumero($commande->generateNumeroCommande())
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setTotal(0)
                    ->setUser($user)
                    ->setIsDelivered(false)
                    ->setIsOrdered(false);
            
            $this->doctrine->getManager()->persist($commande);
            $this->doctrine->getManager()->flush();
        }
    }
}