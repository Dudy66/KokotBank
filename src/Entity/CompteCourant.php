<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Banque;
use App\Entity\Operation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\CompteCourantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteCourantRepository::class)]
class CompteCourant
{
    /**
     * @ORM\OneToMany(targetEntity=Compte::class, mappedBy="compteCourant")
     */
    private $comptes;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $numeroCompte = null;
    #[ORM\Column]
    private ?float $solde = 0.0;
    #[ORM\Column(nullable: true)]
    private ?float $decouvert = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_ouverture = null;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comptesCourants')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;
    #[ORM\ManyToOne(targetEntity: Banque::class, inversedBy: 'compteCourant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Banque $banque = null;
    #[ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'compteCourant')]
    private $operations;
    public function __construct()
    {
        $this->comptes = new ArrayCollection(); // Collection d'objet 
        $this->solde = 0.0;
    }
    public function __toString(): string
    {
        return 'CompteCourant: ' . $this->getId();
    }
    public function getOperations(): Collection
    {
        return $this->operations;
    }
    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }
    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;
        return $this;
    }
    public function getSolde(): ?float
    {
        return $this->solde;
    }
    public function setSolde(float $solde): self
    {
        $this->solde = $solde;
        return $this;
    }
    public function getDecouvert(): ?float
    {
        return $this->decouvert;
    }
    public function setDecouvert(?float $decouvert): self
    {
        $this->decouvert = $decouvert;
        return $this;
    }
    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->date_ouverture;
    }
    public function setDateOuverture(\DateTimeInterface $date_ouverture): self
    {
        $this->date_ouverture = $date_ouverture;
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function setComptes(Collection $comptes): self
    {
        $this->comptes = $comptes;
        return $this;
    }
    public function decouvertAutoriser(float $montant): bool
    {
        return $this->decouvert && ($this->solde - $montant) >= (-$this->decouvert);
    }
    public function ajouter(float $montant): void
    {
        $this->solde += $montant;
    }
    public function retirer(float $montant): bool
    {
        if ($this->decouvertAutoriser($montant)) {
            $this->solde -= $montant;
            return true;
        }
        return false;
    }

    public function virementInterne(float $montant, CompteCourant $destinataire): bool
    {
        if ($this->retirer($montant)) {
            $destinataire->ajouter($montant);
            return true;
        }
        return false;
    }
}
