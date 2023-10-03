<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DataImportController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/data/import', name: 'app_data_import')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson, true);
        foreach ($users as $user) {
            $newUser = new User();
            $newUser ->setNom($user['nom']);
            $newUser ->setPrenom($user['prenom']);
            $email = strtolower($user['prenom']) . "." . strtolower($user['nom']) . "@gmail.com";
            $newUser ->setEmail($email);
            $newUser ->setMdp($user['mdp']);
            $newUser ->setAdresse($user['adresse']);
            $newUser ->setCp($user['cp']);
            $newUser->setVille($user['ville']);
            $newUser->setDateEmbauche($user['dateEmbauche']);

            $entityManager->persist($user);

        }
        $entityManager->flush();
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }
}
