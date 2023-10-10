<?php

namespace App\Controller;

use App\Entity\User;
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

    #[Route('/fiche-frais', name: 'app_data_import_ficheFrais')]
    public function ficheFrais(EntityManagerInterface $entityManager): Response
    {
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson, true);
        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setOldId($user['id']);


            $entityManager->persist($newUser);

        }
        $entityManager->flush();
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }
}
