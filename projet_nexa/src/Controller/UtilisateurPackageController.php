<?php

namespace App\Controller;

use App\Entity\Package;
use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurPackageController extends AbstractController
{
    #[Route('/utilisateur/packages', name: 'app_utilisateur_packages')]
    public function index(PackageRepository $packageRepository): Response
    {
        return $this->render('utilisateur/indexp.html.twig', [
            'packages' => $packageRepository->findAll(),
        ]);
    }

    #[Route('/utilisateur/packages/{id}', name: 'app_utilisateur_package_show')]
    public function show(Package $package): Response
    {
        return $this->render('utilisateur/showp.html.twig', [
            'package' => $package,
        ]);
    }
}
