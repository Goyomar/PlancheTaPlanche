<?php

namespace App\Entity;

use App\Repository\EtoileRepository;
use Doctrine\ORM\Mapping as ORM;

// contrainte unique qui fait un couple unique produit user
    // 10           8           -> allowed
    // 11           7           -> allowed
    // 11           7           -> not allowed, the combination of 11 7 already exists in table

/**
 * @ORM\Table(
 *    name="etoile", 
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="assignment_unique", columns={"produit_id", "user_id"})
 *    }
 * )
 * 
 * @ORM\Entity(repositoryClass=EtoileRepository::class)
 */
class Etoile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="etoiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="etoiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(){
        return strval($this->note);
    }
}
