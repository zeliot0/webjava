<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
#[ORM\Table(name: 'package')]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_package')]
    private ?int $id = null;
    
    #[ORM\Column(name: 'nom_package', length: 255)]
    private ?string $nom = null;

    #[ORM\Column(name: 'description_package', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'type_package', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 50)]
    private ?string $devise = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $uniteDuree = null;

    #[ORM\Column]
    private ?bool $essaiGratuit = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    /* ================= GETTERS & SETTERS ================= */

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
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

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getUniteDuree(): ?string
    {
        return $this->uniteDuree;
    }

    public function setUniteDuree(?string $uniteDuree): self
    {
        $this->uniteDuree = $uniteDuree;
        return $this;
    }

    public function isEssaiGratuit(): ?bool
    {
        return $this->essaiGratuit;
    }

    public function setEssaiGratuit(bool $essaiGratuit): self
    {
        $this->essaiGratuit = $essaiGratuit;
        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }
}
