<?php
// src/Controller/Api/ProjetController.php
namespace App\Controller\Api;

use App\Entity\Projet;
use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api', name: 'api_')]
class ProjetController extends AbstractController
{
#[Route('/projets', name: 'create_projet', methods: ['POST'])]
public function createProjet(
Request $request,
EntityManagerInterface $em,
Security $security
): JsonResponse {
$user = $security->getUser();

if (!$user) {
return $this->json(['error' => 'Utilisateur non connecté'], 401);
}

$employe = $em->getRepository(Employe::class)->findOneBy(['user' => $user]);

if (!$employe) {
return $this->json(['error' => 'Aucun employé lié à cet utilisateur'], 404);
}

$data = json_decode($request->getContent(), true);

if (empty($data['nom']) || empty($data['dateDebut'])) {
return $this->json(['error' => 'Nom et date de début sont obligatoires'], 400);
}

$projet = new Projet();
$projet->setNom($data['nom']);
$projet->setDescription($data['description'] ?? null);

try {
$dateDebut = new \DateTime($data['dateDebut']);
$projet->setDateDebut($dateDebut);
} catch (\Exception $e) {
return $this->json(['error' => 'Date de début invalide'], 400);
}

if (!empty($data['dateFin'])) {
try {
$dateFin = new \DateTime($data['dateFin']);
$projet->setDateFin($dateFin);
} catch (\Exception $e) {
return $this->json(['error' => 'Date de fin invalide'], 400);
}
}

// Lie automatiquement le projet à l'employé connecté
$projet->addEmploye($employe);

$em->persist($projet);
$em->flush();

return $this->json([
'success' => true,
'message' => 'Projet créé avec succès !',
'projet_id' => $projet->getId()
], 201);
}
    #[Route('/projets', name: 'list_projets', methods: ['GET'])]
    public function listProjets(EntityManagerInterface $em): JsonResponse
    {
        $projets = $em->getRepository(Projet::class)->findAll();
        $data = [];

        foreach ($projets as $projet) {
            $employes = [];
            foreach ($projet->getEmployes() as $emp) {
                $employes[] = [
                    'id' => $emp->getId(),
                    'nom' => $emp->getNom(),
                    'prenom' => $emp->getPrenom(),
                ];
            }

            $data[] = [
                'id' => $projet->getId(),
                'nom' => $projet->getNom(),
                'description' => $projet->getDescription(),
                'dateDebut' => $projet->getDateDebut()->format('Y-m-d'),
                'dateFin' => $projet->getDateFin()?->format('Y-m-d'),
                'employes' => $employes,
            ];
        }

        return $this->json($data);
    }
    #[Route('/projets/{id}', name: 'update_projet', methods: ['PUT'])]
    public function updateProjet(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $projet = $em->getRepository(Projet::class)->find($id);

        if (!$projet) {
            return $this->json(['error' => 'Projet non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!empty($data['nom'])) {
            $projet->setNom($data['nom']);
        }

        if (isset($data['description'])) {
            $projet->setDescription($data['description']);
        }

        if (!empty($data['dateDebut'])) {
            try {
                $projet->setDateDebut(new \DateTime($data['dateDebut']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Date début invalide'], 400);
            }
        }

        if (isset($data['dateFin'])) {
            try {
                $projet->setDateFin(new \DateTime($data['dateFin']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Date fin invalide'], 400);
            }
        }

        // MAJ des employés associés si fourni
        if (isset($data['employeIds']) && is_array($data['employeIds'])) {
            $projet->getEmployes()->clear(); // reset la relation
            foreach ($data['employeIds'] as $idEmp) {
                $employe = $em->getRepository(Employe::class)->find($idEmp);
                if ($employe) {
                    $projet->addEmploye($employe);
                }
            }
        }


        $em->flush();

        return $this->json(['success' => true, 'message' => 'Projet mis à jour']);
    }
    #[Route('/projets/{id}', name: 'delete_projet', methods: ['DELETE'])]
    public function deleteProjet(int $id, EntityManagerInterface $em): JsonResponse
    {
        $projet = $em->getRepository(Projet::class)->find($id);

        if (!$projet) {
            return $this->json(['error' => 'Projet non trouvé'], 404);
        }

        $em->remove($projet);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Projet supprimé']);
    }
}

