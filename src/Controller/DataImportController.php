<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/import')]
class DataImportController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user', name: 'app_data_import_user')]
    public function index(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson, true);
        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setOldId($user['id']);
            $newUser->setNom($user['nom']);
            $newUser->setPrenom($user['prenom']);
            $newUser->setLogin($user['login']);
            $email = strtolower($user['prenom']) . "." . strtolower($user['nom']) . "@gmail.com";
            $newUser->setEmail($email);
            $hashedPassword = $passwordHasher->hashPassword($newUser, $user['mdp']);
            $newUser->setPassword($hashedPassword);
            $newUser->setAdresse($user['adresse']);
            $newUser->setCp($user['cp']);
            $newUser->setVille($user['ville']);
            $newUser->setDateEmbauche(new \DateTime($user['dateEmbauche']));

            $entityManager->persist($newUser);

        }
        $entityManager->flush();
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/fichefrais', name: 'app_data_import_ficheFrais')]
    public function ficheFrais(EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $fichesFraisjson = file_get_contents('./ficheFrais.json');
        $fichesFrais = json_decode($fichesFraisjson, true);

        foreach ($fichesFrais as $ficheFrais) {
            $newFf = new FicheFrais();
            $newFf->setMois($ficheFrais['mois']);
            $newFf->setNbJustificatifs($ficheFrais['nbJustificatifs']);
            $newFf->setMontantValid($ficheFrais['montantValide']);
            $newFf->setDateModif(new \DateTime($ficheFrais['dateModif']));
            $user = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $ficheFrais ['idVisiteur']]);
            $newFf->setUser($user);
            switch ($ficheFrais ['idEtat']) {
                case "CL":
                    $etat = $doctrine->getRepository(Etat::class)->find('1');
                    break;
                case "CR":
                    $etat = $doctrine->getRepository(Etat::class)->find('2');
                    break;
                case "RB":
                    $etat = $doctrine->getRepository(Etat::class)->find('3');
                    break;
                case "VA":
                    $etat = $doctrine->getRepository(Etat::class)->find('4');
                    break;
                default:
                    $etat = $doctrine->getRepository(Etat::class)->find('1');
                    break;
            }
            $newFf->setEtat($etat);


            $entityManager->persist($newFf);

        }
        $entityManager->flush();
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/fichefraisHF', name: 'app_data_import_ficheFraisHF')]
    public function ficheFraisHF(EntityManagerInterface $entityManager,  ManagerRegistry $doctrine): Response
    {
        $fichesFraisHFjson = file_get_contents('./lignefraishorsforfait.json');
        $fichesFraisHF = json_decode($fichesFraisHFjson, true);

        foreach ($fichesFraisHF as $ficheFraisHF) {
            $newFfHF = new LigneFraisHorsForfait();
            $newFfHF->setLibelle('libelle');
            $newFfHF->setDate(new \DateTime($ficheFraisHF['date']));
            $newFfHF->setMontant($ficheFraisHF['montant']);
            $user = $doctrine->getRepository(User::class)->findOneBy(['oldId' => $ficheFraisHF['idVisiteur']]);
            $ficheFrais = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $ficheFraisHF['mois']]);
            $newFfHF->setFicheFrais($ficheFrais);

            $entityManager->persist($newFfHF);

        }
        $entityManager->flush();
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }


}
