<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFichesFraisController extends AbstractController
{
    #[Route('/saisirfichefrais', name: 'app_saisir_fiches_frais')]
    public function SaisirFicheFrais(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $ficheFrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $this->getUser(), 'mois'=>date('Ym')]);
        if ($ficheFrais == null){
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($this->getUser());
            $ficheFrais->setMois(date('Ym'));
            $ficheFrais->setDateModif(new \DateTime());
            $ficheFrais->setNbJustificatifs(0);
            $ficheFrais->setMontantValid(0);
            $etatFF = $doctrine->getRepository(Etat::class)->find(2);
            $ficheFrais->setEtat($etatFF);
            $doctrine->getManager()->persist($ficheFrais);
            $doctrine->getManager()->flush();


            $forfaitEtape = $doctrine->getRepository(FraisForfait::class)->find(1);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $forfaitEtape);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            $fraisKm = $doctrine->getRepository(FraisForfait::class)->find(2);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $fraisKm);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            $nuiteHotel = $doctrine->getRepository(FraisForfait::class)->find(3);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $nuiteHotel);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            $repasRestaurant = $doctrine->getRepository(FraisForfait::class)->find(4);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $repasRestaurant);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

        }

        foreach ($ficheFrais->getLigneFraisForfait() as $ligneFraisForfait){
            if ($ligneFraisForfait->getFraisForfait()->getId() == 1){
                $ligneFraisForfaitEtape = $ligneFraisForfait;
            } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 2){
                $ligneFraisForfaitKm = $ligneFraisForfait;
            } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 3){
                $ligneFraisForfaitNuitee = $ligneFraisForfait;
            } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 4){
                $ligneFraisForfaitRepas = $ligneFraisForfait;
            }
        }



        return $this->render('saisir_fiches_frais/index.html.twig', [
            'controller_name' => 'SaisirFichesFraisController',
        ]);
    }
}
