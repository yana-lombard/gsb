<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateComptableController extends AbstractController
{
    #[Route('/create/comptable', name: 'app_create_comptable')]
    public function index(UserPasswordHasherInterface $passwordHasher , EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        // Créez un nouvel utilisateur
        $email = "comptable@gmail.com";
        $existingUser = $userRepository->findOneBy(['email' => $email]);

        if ($existingUser) {
            // L'utilisateur existe déjà, retournez une réponse appropriée
            return $this->render('create_comptable/error.html.twig', [
                'message' => 'Un utilisateur avec cette adresse e-mail existe déjà',
            ]);
        }

        //Création du comptable
        $theUser = new User();
        $theUser->setEmail($email);
        $theUser->setLogin("comptable");
        $theUser->setNom("Dupont");
        $theUser->setPrenom("Jean");
        $theUser->setAdresse("1 rue de la Paix");
        $theUser->setVille("Paris");
        $theUser->setCp("75000");
        $theUser->setDateEmbauche(new \DateTime());
        $theUser->setOldId(0);
        $theUser->setRoles(['ROLE_COMPTABLE']);
        $plainTextPassword = "1234";

        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword(
            $theUser,
            $plainTextPassword
        );
        $theUser->setPassword($hashedPassword);

        // Enregistrer l'utilisateur dans la base de données
        $entityManager->persist($theUser);
        $entityManager->flush();

        return $this->redirectToRoute('app_comptable');
    }
}