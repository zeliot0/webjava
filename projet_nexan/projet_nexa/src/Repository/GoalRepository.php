<?php

namespace App\Repository;

use App\Entity\Goal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Goal>
 */
class GoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goal::class);
    }

    public function searchAndSort(?string $q, ?string $sort, ?string $dir): Query
    {
        $qb = $this->createQueryBuilder('g');

        if ($q) {
            $qb
                ->andWhere(
                    'g.title_goa LIKE :q
                     OR g.description_goa LIKE :q
                     OR g.category_goa LIKE :q
                     OR g.status_goa LIKE :q
                     OR g.priority_goa LIKE :q'
                )
                ->setParameter('q', '%'.$q.'%');
        }

        $allowedSort = [
            'title' => 'g.title_goa',
            'category' => 'g.category_goa',
            'status' => 'g.status_goa',
            'priority' => 'g.priority_goa',
            'progress' => 'g.progress_goa',
            'start' => 'g.date_debut_goa',
            'end' => 'g.date_final_goa',
        ];

        $sortField = $allowedSort[$sort] ?? 'g.id_goa';
        $direction = strtoupper((string) $dir) === 'ASC' ? 'ASC' : 'DESC';

        return $qb->orderBy($sortField, $direction)->getQuery();
    }
}

