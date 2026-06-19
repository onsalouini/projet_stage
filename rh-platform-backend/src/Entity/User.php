<?php
// src/Entity/User.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $createdAt = null;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: Admin::class, cascade: ["persist", "remove"])]
    private ?Admin $admin = null;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: Employe::class, cascade: ["persist", "remove"])]
    private ?Employe $employe = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Dashboard::class, orphanRemoval: true)]
    private Collection $dashboards;
    

    public function __construct()
    {
        $this->dashboards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Ajoute ROLE_USER par défaut
        $roles[] = 'ROLE_USER';
        // Ajoute ROLE_ADMIN si email contient "@admin"
        if (str_contains($this->email, '@admin')) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): self
    {
        $this->admin = $admin;
        if ($admin !== null) {
            $admin->setUser($this);
        }
        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;
        if ($employe !== null) {
            $employe->setUser($this);
        }
        return $this;
    }

    public function getDashboards(): Collection
    {
        return $this->dashboards;
    }

    public function addDashboard(Dashboard $dashboard): self
    {
        if (!$this->dashboards->contains($dashboard)) {
            $this->dashboards[] = $dashboard;
            $dashboard->setUser($this);
        }
        return $this;
    }

    public function removeDashboard(Dashboard $dashboard): self
    {
        if ($this->dashboards->removeElement($dashboard)) {
            if ($dashboard->getUser() === $this) {
                $dashboard->setUser(null);
            }
        }
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
