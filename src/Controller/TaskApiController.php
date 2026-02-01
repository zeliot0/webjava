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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TaskApiController extends AbstractController
{
    #[Route('/api/tasks', name: 'api_tasks_list', methods: ['GET'])]
    public function list(TaskRepository $repo): JsonResponse
    {
        $tasks = $repo->findBy([], ['updateAt' => 'DESC']);
        return $this->json(array_map(fn(Task $t) => $this->toArray($t), $tasks));
    }

    #[Route('/api/tasks', name: 'api_tasks_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $catRepo,
        ValidatorInterface $validator
    ): JsonResponse {
        $d = json_decode($request->getContent(), true) ?? [];

        $task = new Task();

        // hydrate + validate category/due format
        $preErrors = $this->hydrate($task, $d, $catRepo);
        if (!empty($preErrors)) {
            return $this->json([
                'error' => 'Validation failed',
                'errors' => $preErrors,
            ], 422);
        }

        $task->touch();

        // ✅ Symfony validation (Assert)
        $violations = $validator->validate($task);
        if (count($violations) > 0) {
            return $this->json([
                'error' => 'Validation failed',
                'errors' => $this->violationsToErrors($violations),
            ], 422);
        }

        $em->persist($task);
        $em->flush();

        return $this->json($this->toArray($task), 201);
    }

    #[Route('/api/tasks/{id}', name: 'api_tasks_update', methods: ['PATCH','PUT'])]
    public function update(
        Task $task,
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $catRepo,
        ValidatorInterface $validator
    ): JsonResponse {
        $d = json_decode($request->getContent(), true) ?? [];

        $preErrors = $this->hydrate($task, $d, $catRepo);
        if (!empty($preErrors)) {
            return $this->json([
                'error' => 'Validation failed',
                'errors' => $preErrors,
            ], 422);
        }

        $task->touch();

        $violations = $validator->validate($task);
        if (count($violations) > 0) {
            return $this->json([
                'error' => 'Validation failed',
                'errors' => $this->violationsToErrors($violations),
            ], 422);
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

    /**
     * Hydrate Task safely and return pre-validation errors:
     * - dueAt format invalid
     * - categoryId not found
     */
    private function hydrate(Task $t, array $d, CategoryRepository $catRepo): array
    {
        $errors = [];

        if (isset($d['title'])) {
            $t->setTitle((string) $d['title']);
        }

        if (array_key_exists('description', $d)) {
            $desc = $d['description'];
            $t->setDescription(($desc !== null) ? (string)$desc : null);
        }

        // IMPORTANT: set raw values, Validator will check Choice
        if (isset($d['priority'])) {
            $t->setPriority((string)$d['priority']);
        }
        if (isset($d['status'])) {
            $t->setStatus((string)$d['status']);
        }

        // dueAt: accept null or "YYYY-MM-DD"
        if (array_key_exists('dueAt', $d)) {
            $due = $d['dueAt'];

            if ($due === null || $due === '') {
                $t->setDueAt(null);
            } else {
                try {
                    $t->setDueAt(new \DateTimeImmutable((string)$due));
                } catch (\Throwable) {
                    $errors['dueAt'][] = "Format date invalide (ex: 2026-01-31).";
                }
            }
        }

        // categoryId
        if (array_key_exists('categoryId', $d)) {
            if ($d['categoryId'] === null || $d['categoryId'] === '' || $d['categoryId'] === 0) {
                $t->setCategory(null);
            } else {
                $catId = (int) $d['categoryId'];
                $cat = $catRepo->find($catId);
                if (!$cat) {
                    $errors['categoryId'][] = "Catégorie introuvable (id=$catId).";
                } else {
                    $t->setCategory($cat);
                }
            }
        }

        return $errors;
    }

    private function violationsToErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $v) {
            $field = (string) $v->getPropertyPath();
            $errors[$field][] = $v->getMessage();
        }
        return $errors;
    }

    private function toArray(Task $task): array
    {
        $cat = $task->getCategory();

        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'priority' => $task->getPriority(),
            'dueAt' => $task->getDueAt()?->format('Y-m-d'),

            // category info
            'categoryId' => $cat?->getId(),
            'categoryName' => $cat?->getName(),
            'categoryColor' => $cat?->getColor(),
            'categoryIcon' => $cat?->getIcon(),
        ];
    }
}
