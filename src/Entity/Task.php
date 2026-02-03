<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'nexaa_task')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_task', type: 'integer')]
    private ?int $id_task = null;

    #[ORM\Column(name: 'title_task', length: 255)]
    private ?string $title_task = null;

    #[ORM\Column(name: 'desc_task', type: Types::TEXT, nullable: true)]
    private ?string $desc_task = null;

    #[ORM\Column(name: 'status_task', length: 255, nullable: true)]
    private ?string $status_task = null;

    #[ORM\Column(name: 'priority', length: 255, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column(name: 'create_at_task', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createAt_task = null;

    #[ORM\Column(name: 'update_at_task', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updateAt_task = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id_cat', nullable: true, onDelete: 'SET NULL')]
    private ?Category $category = null;

    /**
     * @var Collection<int, Execution>
     */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Execution::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $executions;

    public function __construct()
    {
        $this->executions = new ArrayCollection();
    }

    public function getIdTask(): ?int
    {
        return $this->id_task;
    }

    public function getTitleTask(): ?string
    {
        return $this->title_task;
    }

    public function setTitleTask(string $title_task): static
    {
        $this->title_task = $title_task;
        return $this;
    }

    public function getDescTask(): ?string
    {
        return $this->desc_task;
    }

    public function setDescTask(?string $desc_task): static
    {
        $this->desc_task = $desc_task;
        return $this;
    }

    public function getStatusTask(): ?string
    {
        return $this->status_task;
    }

    public function setStatusTask(?string $status_task): static
    {
        $this->status_task = $status_task;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    public function getCreateAtTask(): ?\DateTimeImmutable
    {
        return $this->createAt_task;
    }

    public function setCreateAtTask(?\DateTimeImmutable $createAt_task): static
    {
        $this->createAt_task = $createAt_task;
        return $this;
    }

    public function getUpdateAtTask(): ?\DateTimeImmutable
    {
        return $this->updateAt_task;
    }

    public function setUpdateAtTask(?\DateTimeImmutable $updateAt_task): static
    {
        $this->updateAt_task = $updateAt_task;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, Execution>
     */
    public function getExecutions(): Collection
    {
        return $this->executions;
    }

    public function addExecution(Execution $execution): static
    {
        if (!$this->executions->contains($execution)) {
            $this->executions->add($execution);
            $execution->setTask($this);
        }
        return $this;
    }

    public function removeExecution(Execution $execution): static
    {
        if ($this->executions->removeElement($execution)) {
            if ($execution->getTask() === $this) {
                $execution->setTask(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->title_task ?? '';
    }
}