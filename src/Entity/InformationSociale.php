<?php

namespace App\Entity;

use App\Repository\InformationSocialeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformationSocialeRepository::class)]
class InformationSociale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'informationSociales')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomContact = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroTelephone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getNomContact(): ?string
    {
        return $this->nomContact;
    }

    public function setNomContact(?string $nomContact): static
    {
        $this->nomContact = $nomContact;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(string $numeroTelephone): static
    {
        $this->numeroTelephone = $numeroTelephone;

        return $this;
    }
}
