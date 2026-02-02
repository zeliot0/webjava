<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
   #[Route('/utilisateur', name: 'app_utilisateur_home')]
public function index(): Response
{
    return $this->redirectToRoute('app_utilisateur_packages');
}

}
