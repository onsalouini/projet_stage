<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Employe;
use App\Entity\Departement;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/users')]
class UserApiController extends AbstractController
{
  #[Route('', name: 'api_users_list', methods: ['GET'])]
  public function list(UserRepository $repo): JsonResponse
  {
    $users = $repo->findAll();

    $data = array_map(function (User $u) {
      $employe = $u->getEmploye();

      return [
        'id' => $u->getId(),
        'email' => $u->getEmail(),
        'roles' => $u->getRoles(),
        'dateCreation' => $u->getCreatedAt()?->format('Y-m-d'),
        'employe' => $employe ? [
          'nom' => $employe->getNom(),
          'prenom' => $employe->getPrenom(),
          'telephone' => $employe->getTelephone(),
          'dateEmbauche' => $employe->getDateEmbauche()?->format('Y-m-d'),
          'departement_id' => $employe->getDepartement()?->getId(),
          'departement_nom' => $employe->getDepartement()?->getNom(),
        ] : null
      ];
    }, $users);

    return $this->json($data);
  }


  #[Route('', name: 'api_users_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles([$data['role'] === 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, 'password')); // Mot de passe par défaut
        $user->setCreatedAt(new \DateTime());

        $employe = new Employe();
        $employe->setNom($data['nom']);
        $employe->setPrenom($data['prenom']);
        $employe->setTelephone($data['telephone'] ?? '');
        $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));
        $employe->setUser($user);

        // 🔗 Lier au département
        if (!empty($data['departement_id'])) {
            $departement = $em->getRepository(Departement::class)->find($data['departement_id']);
            if (!$departement) {
                throw new NotFoundHttpException("Département introuvable.");
            }
            $employe->setDepartement($departement);
        }

        $user->setEmploye($employe);

        $em->persist($user);
        $em->persist($employe);
        $em->flush();

        return $this->json(['status' => 'Utilisateur créé ✅'], 201);
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT'])]
    public function update(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user->setEmail($data['email']);
        $user->setRoles([$data['role'] === 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER']);

        $employe = $user->getEmploye();
        if ($employe) {
            $employe->setNom($data['nom']);
            $employe->setPrenom($data['prenom']);
            $employe->setTelephone($data['telephone']);

            if (!empty($data['dateEmbauche'])) {
                $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));
            }

            if (!empty($data['departement_id'])) {
                $departement = $em->getRepository(Departement::class)->find($data['departement_id']);
                if (!$departement) {
                    throw new NotFoundHttpException("Département introuvable.");
                }
                $employe->setDepartement($departement);
            }
        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['status' => 'Utilisateur mis à jour ✅']);
    }

    #[Route('/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return $this->json(['status' => 'Utilisateur supprimé ❌']);
    }
}
