<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;
    #[ORM\Column]
    private ?string $password = null;
    #[ORM\Column(length: 50)]
    private ?string $nom = null;
    #[ORM\Column(length: 50)]
    private ?string $prenom = null;
    #[ORM\Column(length: 8)]
    private ?string $genre = null;
    #[ORM\Column(length: 60)]
    private ?string $adresse = null;
    #[ORM\Column(length: 50)]
    private ?string $telephone = null;
    #[ORM\Column(type: 'json')]
    private array $userRoles = [];
    #[ORM\Column(nullable: true)]
    private ?string $numeroCompteCourant = null;

    #[ORM\Column(nullable: true)]
    private ?string $numeroCompteAction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastConnexion = null;
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'compte_action_id', referencedColumnName: 'id', nullable: true)]
    private ?CompteAction $compteAction = null;
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CompteCourant::class, cascade: ['persist'])]
    private Collection $comptesCourants;
    #[ORM\ManyToOne(targetEntity: Banque::class, inversedBy: 'user', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'banque_id', referencedColumnName: 'id')]
    private ?Banque $banque = null;
    public function __construct()
    {
        $this->comptesCourants = new ArrayCollection();
        $this->numeroCompteCourant;
        $this->numeroCompteAction;
    }
    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): self
    {
        $this->banque = $banque;

        return $this;
    }
    public function setNumeroCompteCourant(string $numeroCompteCourant): self
    {
        $this->numeroCompteCourant = $numeroCompteCourant;
        return $this;
    }
    public function setNumeroCompteAction(string $numeroCompteAction): self
    {
        $this->numeroCompteAction = $numeroCompteAction;
        return $this;
    }

    public function addCompteCourant(CompteCourant $compteCourant): void
    {
        if (!$this->comptesCourants->contains($compteCourant)) {
            $this->comptesCourants[] = $compteCourant;
            $compteCourant->setUser($this);
        }
    }
    public function removeCompteCourant(CompteCourant $compteCourant): void
    {
        $this->comptesCourants->removeElement($compteCourant);
        $compteCourant->setUser(null);
    }
    public function getCompteAction(): ?CompteAction
    {
        return $this->compteAction;
    }

    public function setCompteAction(?CompteAction $compteAction): self
    {
        $this->compteAction = $compteAction;
        if ($compteAction !== null && $compteAction->getUser() !== $this) {
            $compteAction->setUser($this);
        }
        return $this;
    }

    // Getter Setter
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }
    public function getNomComplet(): ?string
    {
        return $this->prenom . ' ' . $this->nom;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }
    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
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
    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }
    public function setDateCreation(?\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }
    public function getLastConnexion(): ?\DateTimeInterface
    {
        return $this->lastConnexion;
    }
    public function setLastConnexion(?\DateTimeInterface $lastConnexion): self
    {
        $this->lastConnexion = $lastConnexion;
        return $this;
    }
    public function getComptesCourants(): Collection
    {
        return $this->comptesCourants;
    }
    public function setComptesCourants(Collection $comptesCourants): self
    {
        $this->comptesCourants = $comptesCourants;
        return $this;
    }
    public function getRoles(): array
    {
        return $this->userRoles;
    }
    public function setRoles(array $roles): self
    {
        $this->userRoles = $roles;
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
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
    public function eraseCredentials(): void
    {
    }
}
