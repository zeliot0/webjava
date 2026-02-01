<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: '`category`')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la catégorie est obligatoire.")]
    #[Assert\Length(min: 2, max: 255)]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Regex(
        pattern: "/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/",
        message: "Couleur invalide. Exemple: #FFAA00"
    )]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $icon = null;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: "La position doit être >= 0.")]
    private ?int $position = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Choice(choices: ["public", "private", null], message: "Visibility invalide (public ou private).")]
    private ?string $visibility = null;

    #[ORM\Column(name: 'task_limit', nullable: true)]
    #[Assert\PositiveOrZero(message: "Task limit doit être >= 0.")]
    private ?int $taskLimit = null;

    #[ORM\Column(name: 'create_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createAt;

    #[ORM\Column(name: 'update_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ no ne doit pas être vide.")]
    #[Assert\Length(max: 255)]
    private string $no = '';

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Task::class)]
    private Collection $tasks;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createAt = $now;
        $this->updateAt = $now;
        $this->isActive = true;

        $this->no = strtoupper('CAT-' . bin2hex(random_bytes(4)));
        $this->tasks = new ArrayCollection();
    }

    public function touch(): void { $this->updateAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self
    {
        $description = $description !== null ? trim($description) : null;
        $this->description = ($description === '') ? null : $description;
        return $this;
    }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): self
    {
        $color = $color !== null ? trim($color) : null;
        $this->color = ($color === '') ? null : $color;
        return $this;
    }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): self
    {
        $icon = $icon !== null ? trim($icon) : null;
        $this->icon = ($icon === '') ? null : $icon;
        return $this;
    }

    public function getIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }

    public function getPosition(): ?int { return $this->position; }
    public function setPosition(?int $position): self { $this->position = $position; return $this; }

    public function getVisibility(): ?string { return $this->visibility; }
    public function setVisibility(?string $visibility): self
    {
        $visibility = $visibility !== null ? trim($visibility) : null;
        $this->visibility = ($visibility === '') ? null : $visibility;
        return $this;
    }

    public function getTaskLimit(): ?int { return $this->taskLimit; }
    public function setTaskLimit(?int $taskLimit): self { $this->taskLimit = $taskLimit; return $this; }

    public function getCreateAt(): \DateTimeImmutable { return $this->createAt; }
    public function setCreateAt(\DateTimeImmutable $createAt): self { $this->createAt = $createAt; return $this; }

    public function getUpdateAt(): ?\DateTimeImmutable { return $this->updateAt; }
    public function setUpdateAt(?\DateTimeImmutable $updateAt): self { $this->updateAt = $updateAt; return $this; }

    public function getNo(): string { return $this->no; }
    public function setNo(string $no): self { $this->no = trim($no); return $this; }

    /** @return Collection<int, Task> */
    public function getTasks(): Collection { return $this->tasks; }
}
