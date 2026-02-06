<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\MouvementRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// PDF
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $repo): Response
    {
        $q = $request->query->get('q');
        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        return $this->render('produit/index.html.twig', [
            'produits' => $repo->searchAndSort($q, $sort, $dir)->getResult(),
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $produit->setDateAjout(new \DateTime());
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$produit->getDateAjout()) {
                $produit->setDateAjout(new \DateTime());
            }

            /** @var UploadedFile|null $photo */
            $photo = $form->get('photo_p')->getData();
            if ($photo instanceof UploadedFile) {
                $targetDir = sprintf('%s/public/uploads/produits', $this->getParameter('kernel.project_dir'));
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }

                try {
                    $filename = sprintf('%s.%s', bin2hex(random_bytes(16)), $photo->guessExtension() ?: 'jpg');
                    $photo->move($targetDir, $filename);
                    $produit->setPhotoP($filename);
                } catch (\Throwable $e) {
                    $this->addFlash('error', 'Photo: upload échoué.');
                }
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit créé.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }


    #[Route('/stats', name: 'app_produit_stats', methods: ['GET'])]
    public function stats(ProduitRepository $repo): Response
    {
        $stats = $repo->statsByCategorie();

        $totalProduits = 0;
        $totalStock = 0;
        $maxTotalStock = 0;
        foreach ($stats as $row) {
            $totalProduits += (int) ($row['totalProduits'] ?? 0);
            $totalStock += (int) ($row['totalStock'] ?? 0);
            $maxTotalStock = max($maxTotalStock, (int) ($row['totalStock'] ?? 0));
        }

        return $this->render('produit/stats.html.twig', [
            'stats' => $stats,
            'totalProduits' => $totalProduits,
            'totalStock' => $totalStock,
            'maxTotalStock' => $maxTotalStock,
        ]);
    }

    #[Route('/pdf', name: 'app_produit_pdf', methods: ['GET'])]
    public function pdf(Request $request, ProduitRepository $repo): Response
    {
        if (!class_exists(Dompdf::class) || !class_exists(Options::class)) {
            $this->addFlash('error', 'PDF: dompdf/dompdf n\'est pas installé. Exécute: composer require dompdf/dompdf');
            return $this->redirectToRoute('app_produit_index', $request->query->all());
        }

        $q = $request->query->get('q');
        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        $produits = $repo->searchAndSort($q, $sort, $dir)->getResult();

        $html = $this->renderView('produit/pdf.html.twig', [
            'produits' => $produits,
            'generatedAt' => new \DateTimeImmutable(),
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
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
            sprintf('produits_%s.pdf', (new \DateTimeImmutable())->format('Y-m-d'))
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

    #[Route('/{id_p<\\d+>}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit, MouvementRepository $mouvementRepository): Response
    {
        $mouvements = $mouvementRepository->findBy(
            ['produit' => $produit],
            ['date_mouvement' => 'DESC', 'id_mo' => 'DESC'],
            20
        );

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'mouvements' => $mouvements,
        ]);
    }

    #[Route('/{id_p<\\d+>}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $photo */
            $photo = $form->get('photo_p')->getData();
            if ($photo instanceof UploadedFile) {
                $targetDir = sprintf('%s/public/uploads/produits', $this->getParameter('kernel.project_dir'));
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0775, true);
                }

                try {
                    $filename = sprintf('%s.%s', bin2hex(random_bytes(16)), $photo->guessExtension() ?: 'jpg');
                    $photo->move($targetDir, $filename);
                    $produit->setPhotoP($filename);
                } catch (\Throwable $e) {
                    $this->addFlash('error', 'Photo: upload échoué.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Produit mis à jour.');
            return $this->redirectToRoute('app_produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

   #[Route('/{id_p<\\d+>}', name: 'app_produit_delete', methods: ['POST'])]
public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$produit->getIdP(), $request->request->get('_token'))) {

        // 🔴 منع الحذف إذا عندو mouvements
        if (!$produit->getMouvements()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer ce produit : il a des mouvements.');
            return $this->redirectToRoute('app_produit_show', [
                'id_p' => $produit->getIdP()
            ]);
        }

        $em->remove($produit);
        $em->flush();
    }

    return $this->redirectToRoute('app_produit_index');
}



}
