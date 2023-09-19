<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserController extends AbstractController
{
    #[Route('/create/user', name: 'app_create_user')]
    public function index(UserPasswordHasherInterface $passwordHasher , EntityManagerInterface $entityManager): Response
    {
        $theUser = new User();
        $theUser->setEmail("admin@gmail.com");
        $theUser->setRoles(['ROLE_SUPER_ADMIN']);
        $plainTextPassword = "1234";

        $hashedPassword = $passwordHasher->hashPassword(
            $theUser,
            $plainTextPassword
        );
        $theUser->setPassword($hashedPassword);

        $entityManager->persist($theUser);
        $entityManager->flush();

        return $this->render('create_user/index.html.twig', [
            'controller_name' => 'CreateUserController',
        ]);
    }

}