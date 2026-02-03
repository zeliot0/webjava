<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $id_user = null;

    #[ORM\Column(name: 'nom_user', length: 255)]
    private ?string $nom_user = null;

    #[ORM\Column(name: 'mdp_user', length: 255)]
    private ?string $mdp_user = null;

    #[ORM\Column(name: 'email_user', length: 255, unique: true)]
    private ?string $email_user = null;

    #[ORM\Column(name: 'role', type: 'json')]
    private array $role = [];

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): static
    {
        $this->nom_user = $nom_user;
        return $this;
    }

    public function getMdpUser(): ?string
    {
        return $this->mdp_user;
    }

    public function setMdpUser(string $mdp_user): static
    {
        $this->mdp_user = $mdp_user;
        return $this;
    }

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser(string $email_user): static
    {
        $this->email_user = $email_user;
        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole(array $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom_user ?? '';
    }
}