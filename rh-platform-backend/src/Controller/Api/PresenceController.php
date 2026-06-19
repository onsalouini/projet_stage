<?php
// src/Controller/Api/PresenceController.php
namespace App\Controller\Api;

use App\Entity\Presence;
use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api', name: 'api_')]
class PresenceController extends AbstractController
{
    #[Route('/presence', name: 'presence', methods: ['POST'])]
    public function pointerPresence(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        try {
            $user = $security->getUser();

            if (!$user) {
                return $this->json(['error' => 'Utilisateur non connecté'], 401);
            }

            $employe = $em->getRepository(Employe::class)->findOneBy(['user' => $user]);

            if (!$employe) {
                return $this->json(['error' => 'Aucun employé lié à cet utilisateur'], 404);
            }

            $data = json_decode($request->getContent(), true);

            if (empty($data['statut'])) {
                return $this->json(['error' => 'Le statut est obligatoire'], 400);
            }

            $presence = new Presence();
            $presence->setEmploye($employe);
            $presence->setDate(new \DateTime());
            $presence->setStatut($data['statut']);

            if (!empty($data['heureArrivee'])) {
                $heureArrivee = \DateTime::createFromFormat('H:i', $data['heureArrivee']);
                if ($heureArrivee === false) {
                    return $this->json(['error' => 'Format d\'heure d\'arrivée invalide. Utilisez HH:MM'], 400);
                }
                $presence->setHeureArrivee($heureArrivee);
            }

            if (!empty($data['heureDepart'])) {
                $heureDepart = \DateTime::createFromFormat('H:i', $data['heureDepart']);
                if ($heureDepart === false) {
                    return $this->json(['error' => 'Format d\'heure de départ invalide. Utilisez HH:MM'], 400);
                }
                $presence->setHeureDepart($heureDepart);
            }

            $em->persist($presence);
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'Présence enregistrée avec succès !',
                'presence_id' => $presence->getId()
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur serveur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/presences', name: 'presence_list', methods: ['GET'])]
    public function getPresences(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $dateStr = $request->query->get('date');
        if (!$dateStr) {
            return $this->json(['error' => 'Date manquante'], 400);
        }

        try {
            $date = new \DateTime($dateStr);
            $presences = $em->getRepository(Presence::class)->findBy(['date' => $date]);

            $data = array_map(function (Presence $p) {
                $employe = $p->getEmploye();
                return [
                    'id' => $p->getId(),
                    'userId' => $employe->getUser()?->getId(),
                    'userName' => $employe->getPrenom() . ' ' . $employe->getNom(),
                    'date' => $p->getDate()->format('Y-m-d'),
                    'heureArrivee' => $p->getHeureArrivee()?->format('H:i'),
                    'heureDepart' => $p->getHeureDepart()?->format('H:i'),
                    'statut' => $p->getStatut(),
                    'employe' => [
                        'nom' => $employe->getNom(),
                        'prenom' => $employe->getPrenom()
                    ]
                ];
            }, $presences);

            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur serveur', 'details' => $e->getMessage()], 500);
        }
    }
    #[Route('/presences/stats', name: 'presence_stats', methods: ['GET'])]
    public function getStats(EntityManagerInterface $em): JsonResponse
    {
        $presences = $em->getRepository(Presence::class)->findAll();

        $stats = [];
        foreach ($presences as $p) {
            $date = $p->getDate()->format('Y-m-d');
            $statut = $p->getStatut();
            $stats[$date][$statut] = ($stats[$date][$statut] ?? 0) + 1;
        }

        return $this->json($stats);
    }
}