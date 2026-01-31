<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TaskApiController extends AbstractController
{
    private const STATUSES = ['todo','doing','done'];
    private const PRIOS = ['low','med','high'];

    #[Route('/api/tasks', name: 'api_tasks_list', methods: ['GET'])]
    public function list(TaskRepository $repo): JsonResponse
    {
        $tasks = $repo->findBy([], ['updateAt' => 'DESC']);
        return $this->json(array_map(fn(Task $t) => $this->toArray($t), $tasks));
    }

    #[Route('/api/tasks', name: 'api_tasks_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CategoryRepository $catRepo): JsonResponse
    {
        $d = json_decode($request->getContent(), true) ?? [];
        $task = new Task();
        $this->hydrate($task, $d, $catRepo);
        $task->touch();

        if (trim($task->getTitle()) === '') {
            return $this->json(['error' => 'title_required'], 400);
        }

        $em->persist($task);
        $em->flush();

        return $this->json($this->toArray($task), 201);
    }

    #[Route('/api/tasks/{id}', name: 'api_tasks_update', methods: ['PATCH','PUT'])]
    public function update(Task $task, Request $request, EntityManagerInterface $em, CategoryRepository $catRepo): JsonResponse
    {
        $d = json_decode($request->getContent(), true) ?? [];
        $this->hydrate($task, $d, $catRepo);
        $task->touch();

        if (trim($task->getTitle()) === '') {
            return $this->json(['error' => 'title_required'], 400);
        }

        $em->flush();
        return $this->json($this->toArray($task));
    }

    #[Route('/api/tasks/{id}', name: 'api_tasks_delete', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($task);
        $em->flush();
        return $this->json(['ok' => true]);
    }

    private function hydrate(Task $t, array $d, CategoryRepository $catRepo): void
    {
        if (isset($d['title'])) $t->setTitle(trim((string)$d['title']));
        if (array_key_exists('description', $d)) {
            $desc = $d['description'];
            $t->setDescription(($desc !== null && $desc !== '') ? (string)$desc : null);
        }
        if (isset($d['priority']) && in_array($d['priority'], self::PRIOS, true)) {
            $t->setPriority((string)$d['priority']);
        }
        if (isset($d['status']) && in_array($d['status'], self::STATUSES, true)) {
            $t->setStatus((string)$d['status']);
        }
        if (array_key_exists('dueAt', $d)) {
            $due = $d['dueAt'];
            if ($due) {
                try { $t->setDueAt(new \DateTimeImmutable((string)$due)); }
                catch (\Throwable) { $t->setDueAt(null); }
            } else {
                $t->setDueAt(null);
            }
        }
        if (array_key_exists('categoryId', $d)) {
            $cat = $d['categoryId'] ? $catRepo->find((int)$d['categoryId']) : null;
            $t->setCategory($cat);
        }
    }

 private function toArray(\App\Entity\Task $task): array
{
    $cat = $task->getCategory();

    return [
        'id' => $task->getId(),
        'title' => $task->getTitle(),
        'description' => $task->getDescription(),
        'status' => $task->getStatus(),
        'priority' => $task->getPriority(),
        'dueAt' => $task->getDueAt()?->format('Y-m-d'),

        // ✅ Category info for creative badge
        'categoryId' => $cat?->getId(),
        'categoryName' => $cat?->getName(),
        'categoryColor' => $cat?->getColor(),
        'categoryIcon' => $cat?->getIcon(),
    ];
}

}