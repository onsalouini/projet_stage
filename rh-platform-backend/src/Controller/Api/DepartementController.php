<?php
// src/Controller/Api/DepartementController.php
namespace App\Controller\Api;

use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/departements', name: 'api_departements_')]
class DepartementController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $departements = $em->getRepository(Departement::class)->findAll();

        $data = array_map(fn($d) => [
            'id' => $d->getId(),
            'nom' => $d->getNom()
        ], $departements);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom'])) {
            return $this->json(['error' => 'Le nom du département est obligatoire'], 400);
        }

        $departement = new Departement();
        $departement->setNom($data['nom']);

        $em->persist($departement);
        $em->flush();

        return $this->json(['message' => 'Département créé avec succès', 'id' => $departement->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $departement = $em->getRepository(Departement::class)->find($id);

        if (!$departement) {
            return $this->json(['error' => 'Département non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['nom'])) {
            return $this->json(['error' => 'Le nom du département est obligatoire'], 400);
        }

        $departement->setNom($data['nom']);
        $em->flush();

        return $this->json(['message' => 'Département mis à jour avec succès']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $departement = $em->getRepository(Departement::class)->find($id);

        if (!$departement) {
            return $this->json(['error' => 'Département non trouvé'], 404);
        }

        $em->remove($departement);
        $em->flush();

        return $this->json(['message' => 'Département supprimé avec succès']);
    }
}
