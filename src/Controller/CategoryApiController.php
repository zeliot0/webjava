<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CategoryApiController extends AbstractController
{
    #[Route('/api/categories', name: 'api_categories_list', methods: ['GET'])]
    public function list(CategoryRepository $repo): JsonResponse
    {
        $cats = $repo->findBy([], ['id' => 'DESC']);

        $out = [];
        foreach ($cats as $c) {
            $out[] = [
                'id' => $c->getId(),
                'name' => $c->getName(),
                'description' => $c->getDescription(),
                'color' => $c->getColor(),
                'icon' => $c->getIcon(),
                'isActive' => $c->getIsActive(),
                'position' => $c->getPosition(),
                'visibility' => $c->getVisibility(),
                'taskLimit' => $c->getTaskLimit(),
                'createAt' => $c->getCreateAt()->format('Y-m-d H:i:s'),
                'updateAt' => $c->getUpdateAt()?->format('Y-m-d H:i:s'),
                'no' => $c->getNo(),
            ];
        }

        return $this->json($out);
    }

    #[Route('/api/categories', name: 'api_categories_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $d = json_decode($request->getContent(), true);
        if (!is_array($d)) return $this->json(['error' => 'Invalid JSON'], 400);

        $name = trim((string)($d['name'] ?? ''));
        if ($name === '') return $this->json(['error' => 'name is required'], 422);

        $cat = new Category();
        $cat->setName($name);

        // Optionnels
        $desc = $d['description'] ?? null;
        $cat->setDescription(is_string($desc) && trim($desc) !== '' ? trim($desc) : null);

        $color = $d['color'] ?? null;
        $cat->setColor(is_string($color) && trim($color) !== '' ? trim($color) : null);

        $icon = $d['icon'] ?? null;
        $cat->setIcon(is_string($icon) && trim($icon) !== '' ? trim($icon) : null);

        // bool NOT NULL
        $isActive = $d['isActive'] ?? true;
        $cat->setIsActive((bool)$isActive);

        // ints nullable
        $pos = $d['position'] ?? null;
        $cat->setPosition(is_numeric($pos) ? (int)$pos : null);

        $taskLimit = $d['taskLimit'] ?? null;
        $cat->setTaskLimit(is_numeric($taskLimit) ? (int)$taskLimit : null);

        // varchar nullable
        $vis = $d['visibility'] ?? null;
        $cat->setVisibility(is_string($vis) && trim($vis) !== '' ? trim($vis) : null);

        // no NOT NULL (si user veut saisir, sinon auto)
        $no = $d['no'] ?? null;
        if (is_string($no) && trim($no) !== '') {
            $cat->setNo(trim($no));
        }

        // Dates (si user envoie, sinon auto)
        if (!empty($d['createAt'])) {
            $cat->setCreateAt(new \DateTimeImmutable((string)$d['createAt']));
        }
        if (!empty($d['updateAt'])) {
            $cat->setUpdateAt(new \DateTimeImmutable((string)$d['updateAt']));
        } else {
            $cat->touch();
        }

        $em->persist($cat);
        $em->flush();

        return $this->json([
            'id' => $cat->getId(),
            'name' => $cat->getName(),
            'description' => $cat->getDescription(),
            'color' => $cat->getColor(),
            'icon' => $cat->getIcon(),
            'isActive' => $cat->getIsActive(),
            'position' => $cat->getPosition(),
            'visibility' => $cat->getVisibility(),
            'taskLimit' => $cat->getTaskLimit(),
            'createAt' => $cat->getCreateAt()->format('Y-m-d H:i:s'),
            'updateAt' => $cat->getUpdateAt()?->format('Y-m-d H:i:s'),
            'no' => $cat->getNo(),
        ], 201);
    }
}
