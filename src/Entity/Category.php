<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category')]  // Ajout du nom de table explicite
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_cat', type: 'integer')]  // Type explicitement défini
    private ?int $id_cat = null;

    #[ORM\Column(name: 'name_cat', length: 255)]
    private ?string $name_cat = null;

    #[ORM\Column(name: 'desc_cat', type: Types::TEXT, nullable: true)]
    private ?string $desc_cat = null;

    #[ORM\Column(name: 'color_cat', length: 255, nullable: true)]
    private ?string $color_cat = null;

    #[ORM\Column(name: 'icon_cat', length: 255, nullable: true)]
    private ?string $icon_cat = null;

    #[ORM\Column(name: 'is_active', type: 'boolean', nullable: true)]
    private ?bool $isActive = null;

    #[ORM\Column(name: 'position_cat', type: 'integer', nullable: true)]
    private ?int $position_cat = null;

    #[ORM\Column(name: 'visib_cat', length: 255, nullable: true)]
    private ?string $visib_cat = null;

    #[ORM\Column(name: 'tasklimit', type: 'integer', nullable: true)]
    private ?int $tasklimit = null;

    #[ORM\Column(name: 'create_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(name: 'update_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updateAt = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'category', cascade: ['persist', 'remove'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getIdCat(): ?int
    {
        return $this->id_cat;
    }

    public function getNameCat(): ?string
    {
        return $this->name_cat;
    }

    public function setNameCat(string $name_cat): static
    {
        $this->name_cat = $name_cat;
        return $this;
    }

    public function getDescCat(): ?string
    {
        return $this->desc_cat;
    }

    public function setDescCat(?string $desc_cat): static
    {
        $this->desc_cat = $desc_cat;
        return $this;
    }

    public function getColorCat(): ?string
    {
        return $this->color_cat;
    }

    public function setColorCat(?string $color_cat): static
    {
        $this->color_cat = $color_cat;
        return $this;
    }

    public function getIconCat(): ?string
    {
        return $this->icon_cat;
    }

    public function setIconCat(?string $icon_cat): static
    {
        $this->icon_cat = $icon_cat;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getPositionCat(): ?int
    {
        return $this->position_cat;
    }

    public function setPositionCat(?int $position_cat): static
    {
        $this->position_cat = $position_cat;
        return $this;
    }

    public function getVisibCat(): ?string
    {
        return $this->visib_cat;
    }

    public function setVisibCat(?string $visib_cat): static
    {
        $this->visib_cat = $visib_cat;
        return $this;
    }

    public function getTasklimit(): ?int
    {
        return $this->tasklimit;
    }

    public function setTasklimit(?int $tasklimit): static
    {
        $this->tasklimit = $tasklimit;
        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;
        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;
        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setCategory($this);
        }
        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getCategory() === $this) {
                $task->setCategory(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name_cat ?? '';
    }
}