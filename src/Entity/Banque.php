<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanqueRepository::class)]
class Banque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $nom_banque = '';

    #[ORM\OneToMany(mappedBy: 'banque', targetEntity: CompteCourant::class, orphanRemoval: true)]
    private Collection $compteCourant;

    #[ORM\OneToMany(mappedBy: 'banque', targetEntity: CompteAction::class, orphanRemoval: true)]
    private Collection $compteAction;

    #[ORM\OneToMany(mappedBy: 'banque', targetEntity: User::class)]
    private Collection $user;

    public function __construct()
    {
        $this->compteCourant = new ArrayCollection();
        $this->compteAction = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function addCompteCourant(CompteCourant $compteCourant): self
    {
        if (!$this->compteCourant->contains($compteCourant)) {
            $this->compteCourant[] = $compteCourant;
            $compteCourant->setBanque($this); 
        }

        return $this;
    }

    public function removeCompteCourant(CompteCourant $compteCourant): self
    {
        if ($this->compteCourant->removeElement($compteCourant)) {
            if ($compteCourant->getBanque() === $this) {
                $compteCourant->setBanque(null);
            }
        }

        return $this;
    }

    // getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNomBanque(): ?string
    {
        return $this->nom_banque;
    }

    public function setNomBanque(string $nom_banque): self
    {
        $this->nom_banque = $nom_banque;
        return $this;
    }

    /**
     * @return Collection<int, CompteCourant>
     */
    public function getCompteCourant(): Collection
    {
        return $this->compteCourant;
    }


    /**
     * @return Collection<int, CompteAction>
     */
    public function getCompteAction(): Collection
    {
        return $this->compteAction;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

}