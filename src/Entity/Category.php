<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: '`category`')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $visibility = null;

    #[ORM\Column(name: 'task_limit', nullable: true)]
    private ?int $taskLimit = null;

    #[ORM\Column(name: 'create_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createAt;

    #[ORM\Column(name: 'update_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255)]
    private string $no = '';

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createAt = $now;
        $this->updateAt = $now;
        $this->isActive = true;

        // DB: no NOT NULL → on génère une valeur si l'user ne la fournit pas
        $this->no = strtoupper('CAT-' . bin2hex(random_bytes(4)));

        $this->tasks = new ArrayCollection();
    }

    public function touch(): void
    {
        $this->updateAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): self { $this->color = $color; return $this; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): self { $this->icon = $icon; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function getIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }

    public function getPosition(): ?int { return $this->position; }
    public function setPosition(?int $position): self { $this->position = $position; return $this; }

    public function getVisibility(): ?string { return $this->visibility; }
    public function setVisibility(?string $visibility): self { $this->visibility = $visibility; return $this; }

    public function getTaskLimit(): ?int { return $this->taskLimit; }
    public function setTaskLimit(?int $taskLimit): self { $this->taskLimit = $taskLimit; return $this; }

    public function getCreateAt(): \DateTimeImmutable { return $this->createAt; }
    public function setCreateAt(\DateTimeImmutable $createAt): self { $this->createAt = $createAt; return $this; }

    public function getUpdateAt(): ?\DateTimeImmutable { return $this->updateAt; }
    public function setUpdateAt(?\DateTimeImmutable $updateAt): self { $this->updateAt = $updateAt; return $this; }

    public function getNo(): string { return $this->no; }
    public function setNo(string $no): self { $this->no = $no; return $this; }

    /** @return Collection<int, Task> */
    public function getTasks(): Collection { return $this->tasks; }
}
