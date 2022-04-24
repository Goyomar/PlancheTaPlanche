<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Produit $entity, bool $flush = true): void
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
    public function remove(Produit $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function sortBarFind(int $idCategorie = null, bool $promo = null, float $prixUn = null, float $prixDeux = null, int $limit = null, int $offset = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Produit', 'p')
            ->where('p.is_active = true');
        if ($idCategorie !== null) {
            $qb->andWhere('p.categorie = :categorie')
            ->setParameter(':categorie', $idCategorie);
        }
        // if ($taille !== null) {
        //     $qb->andWhere('p.taille = :taille')
        //     ->setParameter(':taille', $taille);
        // }
        if ($promo === true) {
            $qb->andWhere('p.promo > 0');
            // ->setParameter(':promo', $promo);
        }
        if ($prixUn !== null){
            $qb->andWhere('p.prix > :prix')
            ->setParameter(':prix', $prixUn);
        }
        if ($prixDeux !== null){
            $qb->andWhere('p.prix < :prix')
            ->setParameter(':prix', $prixDeux);
        }
        $qb->orderBy('p.created_at','DESC');
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function sortBarCount(int $idCategorie = null, bool $promo = null, float $prixUn = null, float $prixDeux = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('COUNT(p.id)')
            ->from('App\Entity\Produit', 'p')
            ->where('p.is_active = true');
        if ($idCategorie !== null) {
            $qb->andWhere('p.categorie = :categorie')
            ->setParameter(':categorie', $idCategorie);
        }
        // if ($taille !== null) {
        //     $qb->andWhere('p.taille = :taille')
        //     ->setParameter(':taille', $taille);
        // }
        if ($promo === true) {
            $qb->andWhere('p.promo > 0');
            // ->setParameter(':promo', $promo);
        }
        if ($prixUn !== null){
            $qb->andWhere('p.prix > :prix')
            ->setParameter(':prix', $prixUn);
        }
        if ($prixDeux !== null){
            $qb->andWhere('p.prix < :prix')
            ->setParameter(':prix', $prixDeux);
        }
        $qb->orderBy('p.created_at','DESC');
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }
}
