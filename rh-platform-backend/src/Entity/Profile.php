<?php
// src/Entity/Profile.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Employe;

#[ORM\Entity]
#[ORM\Table(name: "profile")]
class Profile
{
    #[ORM\Id]
    #[ORM\OneToOne(inversedBy: "profile", targetEntity: Employe::class)]
    #[ORM\JoinColumn(name: "employe_id", referencedColumnName: "id", nullable: false)]
    private ?Employe $employe = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $photoProfil = null;

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTime $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }
}
