<?php

namespace App\Entity;

use App\Repository\NiveauEtudeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauEtudeRepository::class)]
class NiveauEtude
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, InformationAcademique>
     */
    #[ORM\OneToMany(targetEntity: InformationAcademique::class, mappedBy: 'niveauEtude')]
    private Collection $informationAcademiques;

    public function __construct()
    {
        $this->informationAcademiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $informationAcademique->setNiveauEtude($this);
        }

        return $this;
    }

    public function removeInformationAcademique(InformationAcademique $informationAcademique): static
    {
        if ($this->informationAcademiques->removeElement($informationAcademique)) {
            // set the owning side to null (unless already changed)
            if ($informationAcademique->getNiveauEtude() === $this) {
                $informationAcademique->setNiveauEtude(null);
            }
        }

        return $this;
    }
}
