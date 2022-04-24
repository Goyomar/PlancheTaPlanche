<?php

namespace App\Repository;

use App\Entity\Etoile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etoile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etoile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etoile[]    findAll()
 * @method Etoile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtoileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etoile::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Etoile $entity, bool $flush = true): void
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
    public function remove(Etoile $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findVote(int $userId, int $produitId): ?Etoile
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->setParameter('user', $userId)
            ->andWhere('e.produit = :produit')
            ->setParameter('produit', $produitId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
