<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, InformationAcademique>
     */
    #[ORM\OneToMany(targetEntity: InformationAcademique::class, mappedBy: 'filiere')]
    private Collection $informationAcademiques;

    public function __construct()
    {
        $this->informationAcademiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $informationAcademique->setFiliere($this);
        }

        return $this;
    }

    public function removeInformationAcademique(InformationAcademique $informationAcademique): static
    {
        if ($this->informationAcademiques->removeElement($informationAcademique)) {
            // set the owning side to null (unless already changed)
            if ($informationAcademique->getFiliere() === $this) {
                $informationAcademique->setFiliere(null);
            }
        }

        return $this;
    }
}
