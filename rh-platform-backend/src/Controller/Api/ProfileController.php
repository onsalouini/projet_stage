<?php
// src/Controller/Api/ProfileController.php
namespace App\Controller\Api;

use App\Entity\Employe;
use App\Entity\Profile;
use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/profile', name: 'api_profile_', methods: ['GET', 'POST'])]
class ProfileController extends AbstractController
{
#[Route('', name: 'handle')]
public function handle(
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
return $this->json(['error' => 'Aucun employé associé à cet utilisateur'], 404);
}

$profile = $em->getRepository(Profile::class)->findOneBy(['employe' => $employe]) ?? new Profile();
$profile->setEmploye($employe);

if ($request->isMethod('GET')) {
$data = [
'nom' => $employe->getNom(),
'prenom' => $employe->getPrenom(),
'dateNaissance' => $employe->getDateNaissance()?->format('Y-m-d'),
'telephone' => $employe->getTelephone(),
'adresse' => $employe->getAdresse(),
'dateEmbauche' => $employe->getDateEmbauche()?->format('Y-m-d'),
'departement_id' => $employe->getDepartement()?->getId(),

'bio' => $profile->getBio(),
'photoProfil' => $profile->getPhotoProfil(),
];

return $this->json($data);
}

$data = json_decode($request->getContent(), true);
        // Validation simple
        if (empty($data['nom']) || !is_string($data['nom'])) {
            return $this->json(['error' => 'Le nom est obligatoire et doit être une chaîne'], 400);
        }
        if (empty($data['prenom']) || !is_string($data['prenom'])) {
            return $this->json(['error' => 'Le prénom est obligatoire et doit être une chaîne'], 400);
        }
        if (empty($data['dateEmbauche'])) {
            return $this->json(['error' => 'La date d\'embauche est obligatoire'], 400);
        }

        // Ensuite on peut setter
       
        $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));

        $employe->setNom($data['nom']);
        $employe->setPrenom($data['prenom']);
$employe->setTelephone($data['telephone'] ?? null);
$employe->setAdresse($data['adresse'] ?? null);
$employe->setDateNaissance(!empty($data['dateNaissance']) ? new \DateTime($data['dateNaissance']) : null);
        $employe->setDateEmbauche(new \DateTime($data['dateEmbauche']));

// Nouveau : liaison au département
if (!empty($data['departement_id'])) {
$departement = $em->getRepository(Departement::class)->find($data['departement_id']);
if ($departement) {
$employe->setDepartement($departement);
}
}

$profile->setBio($data['bio'] ?? null);
$profile->setPhotoProfil($data['photoProfil'] ?? null);

$em->persist($employe);
$em->persist($profile);
$em->flush();

return $this->json(['message' => 'Profil et employé mis à jour avec succès ✅']);
}
}