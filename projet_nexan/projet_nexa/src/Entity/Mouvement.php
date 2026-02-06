<?php

namespace App\Entity;
use App\Entity\Produit;
use App\Repository\MouvementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementRepository::class)]
class Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_mo")]
    private ?int $id_mo = null;

    #[ORM\Column(length: 255)]
    private ?string $type_m = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_mouvement = null;

    #[ORM\Column(length: 255)]
    private ?string $motif = null;

     #[ORM\ManyToOne(inversedBy: 'mouvements')]
    #[ORM\JoinColumn(name: "id_p", referencedColumnName: "id_p", nullable: false)]
    private ?Produit $produit = null;

    public function getIdMo(): ?int
    {
        return $this->id_mo;
    }

    public function getTypeM(): ?string
    {
        return $this->type_m;
    }

    public function setTypeM(string $type_m): static
    {
        $this->type_m = $type_m;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateMouvement(): ?\DateTime
    {
        return $this->date_mouvement;
    }

    public function setDateMouvement(\DateTime $date_mouvement): static
    {
        $this->date_mouvement = $date_mouvement;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }
}
