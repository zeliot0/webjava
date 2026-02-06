<?php

namespace App\Repository;

use App\Entity\Mouvement;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mouvement>
 */
class MouvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouvement::class);
    }

    public function search(?string $term): array
    {
        return $this->searchAndSort($term, null, null)->getResult();
    }

    public function searchAndSort(?string $q, ?string $sort, ?string $dir, ?Produit $produit = null): Query
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.produit', 'p')
            ->addSelect('p');

        if ($produit) {
            $qb
                ->andWhere('m.produit = :produit')
                ->setParameter('produit', $produit);
        }

        if ($q) {
            $qb
                ->andWhere('m.type_m LIKE :q OR m.motif LIKE :q OR p.nom_p LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }

        $allowedSort = [
            'type' => 'm.type_m',
            'quantite' => 'm.quantite',
            'date' => 'm.date_mouvement',
            'produit' => 'p.nom_p',
            'nom' => 'p.nom_p',
        ];

        $sortField = $allowedSort[$sort] ?? 'm.date_mouvement';
        $direction = strtoupper((string) $dir) === 'ASC' ? 'ASC' : 'DESC';

        return $qb->orderBy($sortField, $direction)->getQuery();
    }

    public function statsByType(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.type_m AS type, COUNT(m.id_mo) AS totalMouvements, SUM(m.quantite) AS totalQuantite')
            ->groupBy('m.type_m')
            ->orderBy('m.type_m', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


//    /**
//     * @return Mouvement[] Returns an array of Mouvement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mouvement
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
