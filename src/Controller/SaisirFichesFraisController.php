<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\SaisirFichesFFType;
use App\Form\SaisirFichesFHFType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFichesFraisController extends AbstractController
{
    #[Route('/saisirfichefrais', name: 'app_saisir_fiches_frais')]
    public function SaisirFicheFrais(ManagerRegistry $doctrine, Request $request): Response
    {

        //Verifie si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //Recupere la fiche de frais de l'utilisateur connecté
        $ficheFrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $this->getUser(), 'mois'=>date('Ym')]);

        //Création des lignes de frais forfait si la fiche de frais n'existe pas
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

            //Création objects LigneFraisForfait pour forfaitEtpe et l'associe à la fiche frais du user
            $forfaitEtape = $doctrine->getRepository(FraisForfait::class)->find(1);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $forfaitEtape);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            //Création objects LigneFraisForfait pour fraisKm et l'associe à la fiche frais du user
            $fraisKm = $doctrine->getRepository(FraisForfait::class)->find(2);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $fraisKm);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            //Création objects LigneFraisForfait pour nuiteHotel et l'associe à la fiche frais du user
            $nuiteHotel = $doctrine->getRepository(FraisForfait::class)->find(3);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $nuiteHotel);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

            //Création objects LigneFraisForfait pour repasRestaurant et l'associe à la fiche frais du user
            $repasRestaurant = $doctrine->getRepository(FraisForfait::class)->find(4);
            $lignefraisForfait = new LigneFraisForfait(0, $ficheFrais, $repasRestaurant);
            $ficheFrais->addLigneFraisForfait($lignefraisForfait);
            $doctrine->getManager()->persist($lignefraisForfait);
            $doctrine->getManager()->flush();

        }

        //Parcour chaque LigneFraisForfait associé à la ficheFrais du user
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

        //Création du form
        $formFraisForfait = $this->createForm(SaisirFichesFFType::class);
        $formFraisForfait->handleRequest($request);


        if($formFraisForfait->isSubmitted() && $formFraisForfait->isValid()){

            //Met a jour les quantité  de chaque ligne de frais forfait avec les données du form
            $ligneFraisForfaitEtape->setQuantite($formFraisForfait->get('fraisForfaitEtape')->getData());
            $ligneFraisForfaitKm->setQuantite($formFraisForfait->get('fraisForfaitKm')->getData());
            $ligneFraisForfaitNuitee->setQuantite($formFraisForfait->get('fraisForfaitNuitee')->getData());
            $ligneFraisForfaitRepas->setQuantite($formFraisForfait->get('fraisForfaitRepas')->getData());
            $doctrine->getManager()->persist($ligneFraisForfaitEtape);
            $doctrine->getManager()->persist($ligneFraisForfaitKm);
            $doctrine->getManager()->persist($ligneFraisForfaitNuitee);
            $doctrine->getManager()->persist($ligneFraisForfaitRepas);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('app_saisir_fiches_frais', [], Response::HTTP_SEE_OTHER);
        }

        //traitement du form frais hors forfait
        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $ligneFraisHorsForfait->setFicheFrais($ficheFrais);
        $formFraisHorsForfait = $this->createForm(SaisirFichesFHFType::class, $ligneFraisHorsForfait);
        $formFraisHorsForfait->handleRequest($request);

        if($formFraisHorsForfait->isSubmitted() && $formFraisHorsForfait->isValid()){

            //Envoie les données du form dans la base de données
            $ligneFraisHorsForfait->setFicheFrais($ficheFrais);
            $doctrine->getManager()->persist($ligneFraisHorsForfait);
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('app_saisir_fiches_frais', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('saisir_fiches_frais/index.html.twig', [
            'ficheFrais' => $ficheFrais,
            'formFraisForfait' => $formFraisForfait->createView(),
            'formFraisHorsForfait' => $formFraisHorsForfait->createView(),
        ]);
    }
}
