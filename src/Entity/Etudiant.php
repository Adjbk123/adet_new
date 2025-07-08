<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    private ?Village $village = null;

    /**
     * @var Collection<int, InformationAcademique>
     */
    #[ORM\OneToMany(targetEntity: InformationAcademique::class, mappedBy: 'etudiant')]
    private Collection $informationAcademiques;

    /**
     * @var Collection<int, InformationSociale>
     */
    #[ORM\OneToMany(targetEntity: InformationSociale::class, mappedBy: 'etudiant')]
    private Collection $informationSociales;

    /**
     * @var Collection<int, Engagement>
     */
    #[ORM\OneToMany(targetEntity: Engagement::class, mappedBy: 'etudiant')]
    private Collection $engagements;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;



    public function __construct()
    {
        $this->informationAcademiques = new ArrayCollection();
        $this->informationSociales = new ArrayCollection();
        $this->engagements = new ArrayCollection();

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

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): static
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getVillage(): ?Village
    {
        return $this->village;
    }

    public function setVillage(?Village $village): static
    {
        $this->village = $village;

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
            $informationAcademique->setEtudiant($this);
        }

        return $this;
    }

    public function removeInformationAcademique(InformationAcademique $informationAcademique): static
    {
        if ($this->informationAcademiques->removeElement($informationAcademique)) {
            // set the owning side to null (unless already changed)
            if ($informationAcademique->getEtudiant() === $this) {
                $informationAcademique->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InformationSociale>
     */
    public function getInformationSociales(): Collection
    {
        return $this->informationSociales;
    }

    public function addInformationSociale(InformationSociale $informationSociale): static
    {
        if (!$this->informationSociales->contains($informationSociale)) {
            $this->informationSociales->add($informationSociale);
            $informationSociale->setEtudiant($this);
        }

        return $this;
    }

    public function removeInformationSociale(InformationSociale $informationSociale): static
    {
        if ($this->informationSociales->removeElement($informationSociale)) {
            // set the owning side to null (unless already changed)
            if ($informationSociale->getEtudiant() === $this) {
                $informationSociale->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Engagement>
     */
    public function getEngagements(): Collection
    {
        return $this->engagements;
    }

    public function addEngagement(Engagement $engagement): static
    {
        if (!$this->engagements->contains($engagement)) {
            $this->engagements->add($engagement);
            $engagement->setEtudiant($this);
        }

        return $this;
    }

    public function removeEngagement(Engagement $engagement): static
    {
        if ($this->engagements->removeElement($engagement)) {
            // set the owning side to null (unless already changed)
            if ($engagement->getEtudiant() === $this) {
                $engagement->setEtudiant(null);
            }
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }



}
