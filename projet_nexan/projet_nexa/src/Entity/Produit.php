<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_p")]
    private ?int $id_p = null;


    #[ORM\Column(length: 255)]
    private ?string $nom_p = null;

    #[ORM\Column]
    private ?int $quantite_stock = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie_p = null;

    #[ORM\Column(length: 255)]
    private ?string $unite_p = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_ajout = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_expiration = null;

    #[ORM\Column(length: 255)]
    private ?string $emplacement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo_p = null;

    /**
     * @var Collection<int, Mouvement>
     */
    #[ORM\OneToMany(targetEntity: Mouvement::class, mappedBy: 'produit')]
    private Collection $mouvements;
    public function __construct()
    {
        $this->mouvements = new ArrayCollection();
    }

    public function getIdP(): ?int
{
    return $this->id_p;
}


    public function getNomP(): ?string
    {
        return $this->nom_p;
    }

    public function setNomP(string $nom_p): static
    {
        $this->nom_p = $nom_p;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantite_stock;
    }

    public function setQuantiteStock(int $quantite_stock): static
    {
        $this->quantite_stock = $quantite_stock;

        return $this;
    }

    public function getCategorieP(): ?string
    {
        return $this->categorie_p;
    }

    public function setCategorieP(string $categorie_p): static
    {
        $this->categorie_p = $categorie_p;

        return $this;
    }

    public function getUniteP(): ?string
    {
        return $this->unite_p;
    }

    public function setUniteP(string $unite_p): static
    {
        $this->unite_p = $unite_p;

        return $this;
    }

    public function getDateAjout(): ?\DateTime
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTime $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getDateExpiration(): ?\DateTime
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTime $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getPhotoP(): ?string
    {
        return $this->photo_p;
    }

    public function setPhotoP(?string $photo_p): static
    {
        $this->photo_p = $photo_p;

        return $this;
    }

    /**
     * @return Collection<int, Mouvement>
     */
    public function getMouvements(): Collection
    {
        return $this->mouvements;
    }

    public function addMouvement(Mouvement $mouvement): static
    {
        if (!$this->mouvements->contains($mouvement)) {
            $this->mouvements->add($mouvement);
            $mouvement->setProduit($this);
        }

        return $this;
    }

    public function removeMouvement(Mouvement $mouvement): static
    {
        if ($this->mouvements->removeElement($mouvement)) {
            // set the owning side to null (unless already changed)
            if ($mouvement->getProduit() === $this) {
                $mouvement->setProduit(null);
            }
        }

        return $this;
    }
public function __toString(): string
{
    return $this->nom_p;
}

}
