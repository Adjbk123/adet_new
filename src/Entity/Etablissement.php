<?php

namespace App\Entity;

use App\Repository\EtablissementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
class Etablissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etablissements')]
    private ?TypeEtablissement $typeEtablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, InformationAcademique>
     */
    #[ORM\OneToMany(targetEntity: InformationAcademique::class, mappedBy: 'etablissement')]
    private Collection $informationAcademiques;

    public function __construct()
    {
        $this->informationAcademiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeEtablissement(): ?TypeEtablissement
    {
        return $this->typeEtablissement;
    }

    public function setTypeEtablissement(?TypeEtablissement $typeEtablissement): static
    {
        $this->typeEtablissement = $typeEtablissement;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, InformationAcademique>
     */
    public function getInformationAcademiques(): Collection
    {
        return $this->informationAcademiques;
    }

    public function addInformationAcademique(InformationAcademique $informationAcademique): static
    {
        if (!$this->informationAcademiques->contains($informationAcademique)) {
            $this->informationAcademiques->add($informationAcademique);
            $informationAcademique->setEtablissement($this);
        }

        return $this;
    }

    public function removeInformationAcademique(InformationAcademique $informationAcademique): static
    {
        if ($this->informationAcademiques->removeElement($informationAcademique)) {
            // set the owning side to null (unless already changed)
            if ($informationAcademique->getEtablissement() === $this) {
                $informationAcademique->setEtablissement(null);
            }
        }

        return $this;
    }
}
