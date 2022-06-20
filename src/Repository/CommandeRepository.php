<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Commande $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Commande $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findCurrentOrder($userId): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->Where('c.is_ordered = 0')
            ->andWhere('c.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findBuyedOrder($userId): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->Where('c.is_ordered = 1')
            ->andWhere('c.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
