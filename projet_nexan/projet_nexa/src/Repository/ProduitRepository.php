<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // 🔍 Recherche + 🔃 Tri
    public function searchAndSort(?string $q, ?string $sort, ?string $dir): Query
    {
        $qb = $this->createQueryBuilder('p');

        if ($q) {
            $qb->andWhere(
                'p.nom_p LIKE :q 
                 OR p.categorie_p LIKE :q 
                 OR p.unite_p LIKE :q 
                 OR p.emplacement LIKE :q'
            )
            ->setParameter('q', '%'.$q.'%');
        }

        $allowedSort = [
            'nom' => 'p.nom_p',
            'categorie' => 'p.categorie_p',
            'stock' => 'p.quantite_stock',
            'date' => 'p.date_ajout',
        ];

        $sortField = $allowedSort[$sort] ?? 'p.date_ajout';
        $direction = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        return $qb->orderBy($sortField, $direction)->getQuery();
    }

    // 📊 Statistiques
    public function statsByCategorie(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.categorie_p AS categorie, COUNT(p.id_p) AS totalProduits, SUM(p.quantite_stock) AS totalStock')
            ->groupBy('p.categorie_p')
            ->getQuery()
            ->getArrayResult();
    }
}
