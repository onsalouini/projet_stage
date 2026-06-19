<?php

namespace App\Controller\Api;

use App\Entity\Employe;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;

        if (!$email || !$plainPassword) {
            return new JsonResponse(['error' => 'Email et mot de passe requis'], 400);
        }

        // Création utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setCreatedAt(new \DateTime());
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        if (str_contains(strtolower($email), 'admin')) {
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            $user->setRoles(['ROLE_USER']);

            // Créer un employé lié à ce user
            $employe = new Employe();
            $employe->setUser($user);
            $employe->setPrenom('');
            $employe->setNom('');
            $employe->setDateEmbauche(new \DateTime());
            $employe->setAdresse('');
            $employe->setTelephone('');

            $em->persist($employe);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Compte créé avec succès',
            'roles' => $user->getRoles()
        ], 201);
    }

    // SUPPRIMER COMPLÈTEMENT LA MÉTHODE login() (46-90)
}
