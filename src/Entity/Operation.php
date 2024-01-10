<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\CompteCourant;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{   
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $typeOperation = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 150)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(targetEntity: CompteCourant::class, inversedBy: 'operations')]
    #[ORM\JoinColumn(name: 'compteCourant_id', referencedColumnName: 'id', nullable: false)]
    private ?CompteCourant $compteCourant = null;

    public function getCompteCourant(): ?CompteCourant
    {
        return $this->compteCourant;
    }

    public function setCompteCourant(?CompteCourant $compteCourant): self
    {
        $this->compteCourant = $compteCourant;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTypeOperation(): ?string
    {
        return $this->typeOperation;
    }

    public function setTypeOperation(string $typeOperation): static
    {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
