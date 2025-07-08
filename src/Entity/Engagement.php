<?php

namespace App\Entity;

use App\Repository\EngagementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EngagementRepository::class)]
class Engagement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'engagements')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column]
    private ?bool $isParticipeActivite = null;

    #[ORM\Column]
    private ?bool $isSouhaiteDevenirMembre = null;

    #[ORM\Column]
    private ?bool $isSouhaiteIntegrerCommission = null;

    #[ORM\ManyToOne(inversedBy: 'engagements')]
    private ?Commission $commission = null;

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

    public function isParticipeActivite(): ?bool
    {
        return $this->isParticipeActivite;
    }

    public function setIsParticipeActivite(bool $isParticipeActivite): static
    {
        $this->isParticipeActivite = $isParticipeActivite;

        return $this;
    }

    public function isSouhaiteDevenirMembre(): ?bool
    {
        return $this->isSouhaiteDevenirMembre;
    }

    public function setIsSouhaiteDevenirMembre(bool $isSouhaiteDevenirMembre): static
    {
        $this->isSouhaiteDevenirMembre = $isSouhaiteDevenirMembre;

        return $this;
    }

    public function isSouhaiteIntegrerCommission(): ?bool
    {
        return $this->isSouhaiteIntegrerCommission;
    }

    public function setIsSouhaiteIntegrerCommission(bool $isSouhaiteIntegrerCommission): static
    {
        $this->isSouhaiteIntegrerCommission = $isSouhaiteIntegrerCommission;

        return $this;
    }

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    public function setCommission(?Commission $commission): static
    {
        $this->commission = $commission;

        return $this;
    }
}
