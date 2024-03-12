<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\MonthSelectorFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFichesController extends AbstractController
{
    #[Route('/mesFiches', name: 'app_mesFiches')]
    public function selectMonth(Request $request, EntityManagerInterface $entityManager): Response
    {
        $selectedFiche = null;
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $mesFiches = $entityManager->getRepository(FicheFrais::class)->findBy(['user'=>$this->getUser()]);
        $form = $this->createForm(MonthSelectorFormType::class, $mesFiches);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('selectedMonth')->getData();

        }

        return $this->render('month_selector/select.html.twig', [
            'controlleur_name' => 'MesFichesControlleur',
            'form' => $form->createView(),
            'selectedFiche' => $selectedFiche,
        ]);
    }

}
