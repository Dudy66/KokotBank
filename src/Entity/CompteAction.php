<?php

namespace App\Entity;

use App\Service;
use App\Repository\CompteActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteActionRepository::class)]
class CompteAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @ORM\Column(nullable=true)
     */
    #[ORM\Column(nullable: true)]
    private ?string $numeroCompte = null;
    #[ORM\Column]
    private ?float $solde = 0.0;
    #[ORM\Column(nullable: true)]
    private ?float $plafond = null;
    #[ORM\Column]
    private ?float $interet = null;
    #[ORM\ManyToOne(inversedBy: 'compteAction')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Banque $banque = null;
    #[ORM\OneToOne(inversedBy: 'compteAction', cascade: ['persist', 'remove'])]
    private ?User $user = null;
    public function __construct()
    {
        $this->solde = 0.0;
        $this->interet = 1.7;
    }
    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }
    public function setNumeroCompte(?string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;
        return $this;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        if ($user !== null && $user->getCompteAction() !== $this) {
            $user->setCompteAction($this);
        }
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getPlafond(): ?float
    {
        return $this->plafond;
    }
    public function setPlafond(?float $plafond): static
    {
        $this->plafond = $plafond;
        return $this;
    }
    public function getInteret(): ?float
    {
        return $this->interet;
    }
    public function setInteret(float $interet): static
    {
        $this->interet = $interet;
        return $this;
    }
    public function getBanque(): ?Banque
    {
        return $this->banque;
    }
    public function setBanque(?Banque $banque): static
    {
        $this->banque = $banque;
        return $this;
    }
    public function ajouter(float $montant): void
    {
        $this->solde += $montant;
    }
    public function retirer(float $montant): bool
    {
        if ($this->solde >= $montant) {
            $this->solde -= $montant;
            return true;
        }
        return false;
    }

    public function virementInterne(float $montant, CompteAction $destinataire): bool
    {
        if ($this->retirer($montant)) {
            $destinataire->ajouter($montant);
            return true;
        }
        return false;
    }
}
