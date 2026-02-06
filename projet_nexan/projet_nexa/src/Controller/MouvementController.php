<?php

namespace App\Controller;

use App\Entity\Mouvement;
use App\Form\MouvementType;
use App\Repository\MouvementRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// PDF
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/mouvement')]
final class MouvementController extends AbstractController
{
    private function stockDelta(?string $type, int $quantite): int
    {
        $type = strtoupper((string) $type);
        if ($type === 'ENTREE') {
            return $quantite;
        }
        if ($type === 'SORTIE') {
            return -$quantite;
        }
        return 0;
    }

    #[Route('', name: 'app_mouvement_index', methods: ['GET'])]
    public function index(Request $request, MouvementRepository $repo, ProduitRepository $produitRepository): Response
    {
        $q = $request->query->get('q');
        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        $idP = $request->query->getInt('id_p');
        $produitFilter = null;
        if ($idP > 0) {
            $produitFilter = $produitRepository->find($idP);
        }

        return $this->render('mouvement/index.html.twig', [
            'mouvements' => $repo->searchAndSort($q, $sort, $dir, $produitFilter)->getResult(),
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
            'produitFilter' => $produitFilter,
        ]);
    }

    #[Route('/stats', name: 'app_mouvement_stats', methods: ['GET'])]
    public function stats(MouvementRepository $repo): Response
    {
        $stats = $repo->statsByType();

        $totalMouvements = 0;
        $totalQuantite = 0;
        $maxTotalQuantite = 0;
        foreach ($stats as $row) {
            $totalMouvements += (int) ($row['totalMouvements'] ?? 0);
            $totalQuantite += (int) ($row['totalQuantite'] ?? 0);
            $maxTotalQuantite = max($maxTotalQuantite, (int) ($row['totalQuantite'] ?? 0));
        }

        return $this->render('mouvement/stats.html.twig', [
            'stats' => $stats,
            'totalMouvements' => $totalMouvements,
            'totalQuantite' => $totalQuantite,
            'maxTotalQuantite' => $maxTotalQuantite,
        ]);
    }

    #[Route('/pdf', name: 'app_mouvement_pdf', methods: ['GET'])]
    public function pdf(Request $request, MouvementRepository $repo, ProduitRepository $produitRepository): Response
    {
        if (!class_exists(Dompdf::class) || !class_exists(Options::class)) {
            $this->addFlash('error', 'PDF: dompdf/dompdf n\'est pas installé. Exécute: composer require dompdf/dompdf');
            return $this->redirectToRoute('app_mouvement_index', $request->query->all());
        }

        $q = $request->query->get('q');
        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        $idP = $request->query->getInt('id_p');
        $produitFilter = null;
        if ($idP > 0) {
            $produitFilter = $produitRepository->find($idP);
        }

        $mouvements = $repo->searchAndSort($q, $sort, $dir, $produitFilter)->getResult();

        $html = $this->renderView('mouvement/pdf.html.twig', [
            'mouvements' => $mouvements,
            'generatedAt' => new \DateTimeImmutable(),
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
            'produit' => $produitFilter,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            sprintf('mouvements_%s.pdf', (new \DateTimeImmutable())->format('Y-m-d'))
        );

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition,
            ]
        );
    }

    #[Route('/new', name: 'app_mouvement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $mouvement = new Mouvement();
        $mouvement->setDateMouvement(new \DateTime());

        $idP = $request->query->getInt('id_p');
        if ($idP > 0) {
            $produit = $produitRepository->find($idP);
            if ($produit) {
                $mouvement->setProduit($produit);
            }
        }

        $form = $this->createForm(MouvementType::class, $mouvement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit = $mouvement->getProduit();
            $quantite = (int) $mouvement->getQuantite();
            $delta = $this->stockDelta($mouvement->getTypeM(), $quantite);

            if ($produit) {
                $stockActuel = (int) $produit->getQuantiteStock();
                $stockApres = $stockActuel + $delta;

                if ($stockApres < 0) {
                    $form->get('quantite')->addError(new FormError('Stock insuffisant pour cette sortie.'));
                } else {
                    $produit->setQuantiteStock($stockApres);

                    $entityManager->persist($mouvement);
                    $entityManager->flush();

                    $this->addFlash('success', 'Mouvement créé. Stock mis à jour.');
                    return $this->redirectToRoute('app_mouvement_index');
                }
            } else {
                $entityManager->persist($mouvement);
                $entityManager->flush();

                $this->addFlash('success', 'Mouvement créé.');
                return $this->redirectToRoute('app_mouvement_index');
            }
        }

        return $this->render('mouvement/new.html.twig', [
            'mouvement' => $mouvement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_mo<\\d+>}', name: 'app_mouvement_show', methods: ['GET'])]
    public function show(Mouvement $mouvement): Response
    {
        return $this->render('mouvement/show.html.twig', [
            'mouvement' => $mouvement,
        ]);
    }

    #[Route('/{id_mo<\\d+>}/edit', name: 'app_mouvement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mouvement $mouvement, EntityManagerInterface $entityManager): Response
    {
        $oldProduit = $mouvement->getProduit();
        $oldQuantite = (int) $mouvement->getQuantite();
        $oldType = (string) $mouvement->getTypeM();

        $form = $this->createForm(MouvementType::class, $mouvement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newProduit = $mouvement->getProduit();
            $newQuantite = (int) $mouvement->getQuantite();
            $newType = (string) $mouvement->getTypeM();

            $revertDelta = -$this->stockDelta($oldType, $oldQuantite);
            $applyDelta = $this->stockDelta($newType, $newQuantite);

            if (!$oldProduit || !$newProduit) {
                $entityManager->flush();

                $this->addFlash('success', 'Mouvement mis à jour.');
                return $this->redirectToRoute('app_mouvement_index');
            }

            if ($oldProduit === $newProduit) {
                $stockActuel = (int) $oldProduit->getQuantiteStock();
                $stockFinal = $stockActuel + $revertDelta + $applyDelta;

                if ($stockFinal < 0) {
                    $form->get('quantite')->addError(new FormError('Stock insuffisant pour cette sortie.'));
                } else {
                    $oldProduit->setQuantiteStock($stockFinal);
                    $entityManager->flush();

                    $this->addFlash('success', 'Mouvement mis à jour. Stock ajusté.');
                    return $this->redirectToRoute('app_mouvement_index');
                }
            } else {
                $oldStockActuel = (int) $oldProduit->getQuantiteStock();
                $oldStockFinal = $oldStockActuel + $revertDelta;

                $newStockActuel = (int) $newProduit->getQuantiteStock();
                $newStockFinal = $newStockActuel + $applyDelta;

                if ($oldStockFinal < 0) {
                    $form->addError(new FormError('Modification impossible: stock du produit original deviendra négatif.'));
                } elseif ($newStockFinal < 0) {
                    $form->get('quantite')->addError(new FormError('Stock insuffisant pour cette sortie.'));
                } else {
                    $oldProduit->setQuantiteStock($oldStockFinal);
                    $newProduit->setQuantiteStock($newStockFinal);
                    $entityManager->flush();

                    $this->addFlash('success', 'Mouvement mis à jour. Stock ajusté.');
                    return $this->redirectToRoute('app_mouvement_index');
                }
            }

        }

        return $this->render('mouvement/edit.html.twig', [
            'mouvement' => $mouvement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_mo<\\d+>}', name: 'app_mouvement_delete', methods: ['POST'])]
    public function delete(Request $request, Mouvement $mouvement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mouvement->getIdMo(), $request->request->get('_token'))) {
            $produit = $mouvement->getProduit();

            if ($produit) {
                $quantite = (int) $mouvement->getQuantite();
                $revertDelta = -$this->stockDelta($mouvement->getTypeM(), $quantite);

                $stockActuel = (int) $produit->getQuantiteStock();
                $stockApres = $stockActuel + $revertDelta;

                if ($stockApres < 0) {
                    $this->addFlash('error', 'Suppression impossible: le stock deviendra négatif.');
                    return $this->redirectToRoute('app_mouvement_show', ['id_mo' => $mouvement->getIdMo()]);
                }

                $produit->setQuantiteStock($stockApres);
            }

            $entityManager->remove($mouvement);
            $entityManager->flush();

            $this->addFlash('success', 'Mouvement supprimé. Stock ajusté.');
        }

        return $this->redirectToRoute('app_mouvement_index');
    }
    
}
