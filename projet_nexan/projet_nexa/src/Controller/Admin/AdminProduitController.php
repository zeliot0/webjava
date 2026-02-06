<?php

namespace App\Controller\Admin;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/produits')]
class AdminProduitController extends AbstractController
{
    #[Route('', name: 'admin_produit_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $repo): Response
    {
        return $this->render('admin/produit/index.html.twig', [
            'produits' => $repo->searchAndSort(
                $request->query->get('q'),
                $request->query->get('sort'),
                $request->query->get('dir')
            )->getResult(),
            'q' => $request->query->get('q'),
            'is_admin' => true
        ]);
    }
}
