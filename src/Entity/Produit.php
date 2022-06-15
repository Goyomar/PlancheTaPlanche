<?php

namespace App\Entity;

use Monolog\DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $promo;

    /**
     * @ORM\Column(type="string")
     */
    private $taille;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_custom;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=Skateboard::class, mappedBy="composer")
     */
    private $skateboards;

    /**
     * @ORM\OneToMany(targetEntity=Etoile::class, mappedBy="produit", orphanRemoval=true)
     */
    private $etoiles;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="produit", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    public function __construct()
    {
        $this->skateboards = new ArrayCollection();
        $this->etoiles = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->created_at = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPromo(): ?int
    {
        return $this->promo;
    }

    public function setPromo(?int $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsCustom(): ?bool
    {
        return $this->is_custom;
    }

    public function setIsCustom(bool $is_custom): self
    {
        $this->is_custom = $is_custom;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Skateboard>
     */
    public function getSkateboards(): Collection
    {
        return $this->skateboards;
    }

    public function addSkateboard(Skateboard $skateboard): self
    {
        if (!$this->skateboards->contains($skateboard)) {
            $this->skateboards[] = $skateboard;
            $skateboard->addComposer($this);
        }

        return $this;
    }

    public function removeSkateboard(Skateboard $skateboard): self
    {
        if ($this->skateboards->removeElement($skateboard)) {
            $skateboard->removeComposer($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Etoile>
     */
    public function getEtoiles(): Collection
    {
        return $this->etoiles;
    }

    public function addEtoile(Etoile $etoile): self
    {
        if (!$this->etoiles->contains($etoile)) {
            $this->etoiles[] = $etoile;
            $etoile->setProduit($this);
        }

        return $this;
    }

    public function removeEtoile(Etoile $etoile): self
    {
        if ($this->etoiles->removeElement($etoile)) {
            // set the owning side to null (unless already changed)
            if ($etoile->getProduit() === $this) {
                $etoile->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setProduit($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getProduit() === $this) {
                $commentaire->setProduit(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function __toString(): string{
        return $this->nom;
    }

    public function getAverageEtoiles(){
        $average = 0;
        $etoiles = $this->getEtoiles()->toArray();
        if(count($etoiles) == 0 ){
            return 0;
            die();
        }
        foreach ($etoiles as $etoile) {
            $average += $etoile->getNote();
        }
        return round($average / count($etoiles), 2);
    }
}
