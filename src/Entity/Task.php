<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: '`task`')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne doit pas dépasser {{ limit }} caractères."
    )]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(
        choices: ['todo','doing','done'],
        message: "Statut invalide (todo, doing, done)."
    )]
    private string $status = 'todo'; // todo|doing|done

    #[ORM\Column(length: 20)]
    #[Assert\Choice(
        choices: ['low','med','high'],
        message: "Priorité invalide (low, med, high)."
    )]
    private string $priority = 'med'; // low|med|high

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[Assert\Type(type: \DateTimeInterface::class, message: "Date limite invalide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date limite ne peut pas être dans le passé.")]
    private ?\DateTimeImmutable $dueAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updateAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createAt = $now;
        $this->updateAt = $now;
    }

    public function touch(): void
    {
        $this->updateAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self
    {
        $this->title = trim($title);
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self
    {
        $description = $description !== null ? trim($description) : null;
        $this->description = ($description === '') ? null : $description;
        return $this;
    }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getPriority(): string { return $this->priority; }
    public function setPriority(string $priority): self { $this->priority = $priority; return $this; }

    public function getDueAt(): ?\DateTimeImmutable { return $this->dueAt; }
    public function setDueAt(?\DateTimeImmutable $dueAt): self { $this->dueAt = $dueAt; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }

    public function getCreateAt(): \DateTimeImmutable { return $this->createAt; }
    public function setCreateAt(\DateTimeImmutable $d): self { $this->createAt = $d; return $this; }

    public function getUpdateAt(): \DateTimeImmutable { return $this->updateAt; }
    public function setUpdateAt(\DateTimeImmutable $d): self { $this->updateAt = $d; return $this; }
}
