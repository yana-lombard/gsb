<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFichesFraisController extends AbstractController
{
    #[Route('/saisir/fiches/frais', name: 'app_saisir_fiches_frais')]
    public function index(): Response
    {
        return $this->render('saisir_fiches_frais/index.html.twig', [
            'controller_name' => 'SaisirFichesFraisController',
        ]);
    }
}
