<?php

namespace App\Controller\Admin;

use App\Repository\MouvementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/mouvements')]
class AdminMouvementController extends AbstractController
{
    #[Route('', name: 'admin_mouvement_index', methods: ['GET'])]
    public function index(Request $request, MouvementRepository $repo): Response
    {
        return $this->render('admin/mouvement/index.html.twig', [
            'mouvements' => $repo->searchAndSort(
                $request->query->get('q'),
                $request->query->get('sort'),
                $request->query->get('dir')
            )->getResult(),
            'q' => $request->query->get('q'),
        ]);
    }
}
