<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PresenceRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PresenceRepository::class)]
#[ORM\Table(name: "presence")]
class Presence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employe::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Employe $employe = null;

    #[ORM\Column(type: "date")]
    #[Assert\NotNull]
    private ?\DateTime $date = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(['Présent', 'Absent', 'En retard'])]
    private ?string $statut = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Assert\Time]
    private ?\DateTime $heureArrivee = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Assert\Time]
    private ?\DateTime $heureDepart = null;

    // Getters et setters...
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;
        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getHeureArrivee(): ?\DateTime
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(?\DateTime $heureArrivee): self
    {
        $this->heureArrivee = $heureArrivee;
        return $this;
    }

    public function getHeureDepart(): ?\DateTime
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(?\DateTime $heureDepart): self
    {
        $this->heureDepart = $heureDepart;
        return $this;
    }
}
