<?php

namespace App\Controller\Api;

use App\Entity\Employe;
use App\Entity\User;
use App\Repository\EmployeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/employes', name: 'api_employes_')]
class EmployeController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EmployeRepository $repo): JsonResponse
    {
        $data = array_map(function (Employe $e) {
            return [
                'id' => $e->getId(),
                'prenom' => $e->getPrenom(),
                'nom' => $e->getNom(),
                'email' => $e->getUser()?->getEmail(),
                'dateEmbauche' => $e->getDateEmbauche()?->format('Y-m-d'),
                'departement' => $e->getDepartement()?->getNom()
            ];
        }, $repo->findAll());

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $userRepo->findOneBy(['email' => $data['email']]);

        if (!$user) return $this->json(['error' => 'Utilisateur non trouvé'], 404);

        $employe = new Employe();
        $employe->setUser($user);
        $employe->setNom($data['nom']);
        $employe->setPrenom($data['prenom']);
        $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));

        $em->persist($employe);
        $em->flush();

        return $this->json(['message' => 'Employé créé ✅']);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, EmployeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $employe = $repo->find($id);
        if (!$employe) return $this->json(['error' => 'Employé introuvable'], 404);

        $data = json_decode($request->getContent(), true);
        $employe->setNom($data['nom'] ?? $employe->getNom());
        $employe->setPrenom($data['prenom'] ?? $employe->getPrenom());
        $em->flush();

        return $this->json(['message' => 'Employé mis à jour 🔄']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EmployeRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $employe = $repo->find($id);
        if (!$employe) return $this->json(['error' => 'Employé introuvable'], 404);

        $em->remove($employe);
        $em->flush();

        return $this->json(['message' => 'Employé supprimé 🗑']);
    }
}
