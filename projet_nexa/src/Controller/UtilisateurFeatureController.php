<?php

namespace App\Controller;

use App\Entity\Feature;
use App\Repository\FeatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurFeatureController extends AbstractController
{
    #[Route('/utilisateur/features', name: 'app_utilisateur_features')]
    public function index(FeatureRepository $featureRepository): Response
    {
        return $this->render('utilisateur/indexf.html.twig', [
            'features' => $featureRepository->findAll(),
        ]);
    }

    #[Route('/utilisateur/features/{id}', name: 'app_utilisateur_feature_show')]
    public function show(Feature $feature): Response
    {
        return $this->render('utilisateur/showf.html.twig', [
            'feature' => $feature,
        ]);
    }
}
