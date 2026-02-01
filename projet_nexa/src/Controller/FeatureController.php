<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Form\FeatureType;
use App\Repository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feature')]
final class FeatureController extends AbstractController
{
    #[Route('', name: 'app_feature_index', methods: ['GET'])]
    public function index(FeatureRepository $featureRepository): Response
    {
        return $this->render('feature/index.html.twig', [
            'features' => $featureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_feature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feature = new Feature();

        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ هذا هو السطر اللي يصلّح الغلطة
            $feature->setDateCreation(new \DateTime());

            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('app_feature_index');
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feature_show', methods: ['GET'])]
    public function show(Feature $feature): Response
    {
        return $this->render('feature/show.html.twig', [
            'feature' => $feature,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_feature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feature $feature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_feature_index');
        }

        return $this->render('feature/edit.html.twig', [
            'feature' => $feature,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feature_delete', methods: ['POST'])]
    public function delete(Request $request, Feature $feature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$feature->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($feature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_feature_index');
    }
}
