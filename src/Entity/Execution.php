<?php

namespace App\Entity;

use App\Repository\ExecutionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExecutionRepository::class)]
#[ORM\Table(name: 'nexaa_execution')]
class Execution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_ex', type: 'integer')]
    private ?int $id_ex = null;

    #[ORM\Column(name: 'title_ex', length: 255)]
    private ?string $title_ex = null;

    #[ORM\Column(name: 'desc_ex', type: Types::TEXT, nullable: true)]
    private ?string $desc_ex = null;

    #[ORM\Column(name: 'status_ex', length: 255, nullable: true)]
    private ?string $status_ex = null;

    #[ORM\Column(name: 'position_ex', type: 'integer', nullable: true)]
    private ?int $position_ex = null;

    #[ORM\Column(name: 'create_at_ex', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createAt_ex = null;

    #[ORM\Column(name: 'update_at_ex', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updateAt_ex = null;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'executions')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id_task', nullable: false, onDelete: 'CASCADE')]
    private ?Task $task = null;

    public function getIdEx(): ?int
    {
        return $this->id_ex;
    }

    public function getTitleEx(): ?string
    {
        return $this->title_ex;
    }

    public function setTitleEx(string $title_ex): static
    {
        $this->title_ex = $title_ex;
        return $this;
    }

    public function getDescEx(): ?string
    {
        return $this->desc_ex;
    }

    public function setDescEx(?string $desc_ex): static
    {
        $this->desc_ex = $desc_ex;
        return $this;
    }

    public function getStatusEx(): ?string
    {
        return $this->status_ex;
    }

    public function setStatusEx(?string $status_ex): static
    {
        $this->status_ex = $status_ex;
        return $this;
    }

    public function getPositionEx(): ?int
    {
        return $this->position_ex;
    }

    public function setPositionEx(?int $position_ex): static
    {
        $this->position_ex = $position_ex;
        return $this;
    }

    public function getCreateAtEx(): ?\DateTimeImmutable
    {
        return $this->createAt_ex;
    }

    public function setCreateAtEx(?\DateTimeImmutable $createAt_ex): static
    {
        $this->createAt_ex = $createAt_ex;
        return $this;
    }

    public function getUpdateAtEx(): ?\DateTimeImmutable
    {
        return $this->updateAt_ex;
    }

    public function setUpdateAtEx(?\DateTimeImmutable $updateAt_ex): static
    {
        $this->updateAt_ex = $updateAt_ex;
        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;
        return $this;
    }

    public function __toString(): string
    {
        return $this->title_ex ?? '';
    }
}