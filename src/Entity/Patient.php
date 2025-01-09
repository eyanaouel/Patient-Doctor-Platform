<?php

namespace App\Entity;

use App\Entity\Medecin;
use App\Entity\RDV;
use App\Form\RDVType;
use App\Repository\PatientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\GroupeSanguin; // Enum de groupe sanguin
use Symfony\Component\Validator\Constraints\Date; // Ajoutez cette ligne
use App\Entity\HistoriqueMedical;





#[ORM\Entity(repositoryClass: PatientRepository::class)]
class Patient extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $idP = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroCNAM = null;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: 'La CIN doit comporter exactement {{ limit }} caractères.'
    )]
    private ?string $CIN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\Date] 
    private ?\DateTimeInterface $dateNaissance = null;

    

    /**
     * @var Collection<int, RDV>
     */
    #[ORM\OneToMany(targetEntity: RDV::class, mappedBy: 'patient')]
    private Collection $RDVs;

    /**
     * @var Collection<int, HistoriqueMedical>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueMedical::class, mappedBy: 'patient')]
    private Collection $historiquesMedicales;

    public function __construct()
    {
        $this->RDVs = new ArrayCollection();
        $this->historiquesMedicales = new ArrayCollection();
    }

    public function getIdP(): ?int
    {
        return $this->idP;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroTel = null;
    public function getnumeroTel(): ?string
    {
        return $this->numeroTel;
    }

    public function setnumeroTel(?string $numeroTel): static
    {
        $this->numeroTel = $numeroTel;
        return $this;
    }



    public function getNumeroCNAM(): ?string
    {
        return $this->numeroCNAM;
    }

    public function setNumeroCNAM(?string $numeroCNAM): static
    {
        $this->numeroCNAM = $numeroCNAM;
        return $this;
    }

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(string $CIN): static
    {
        $this->CIN = $CIN;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }


public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
{
    $this->dateNaissance = $dateNaissance;
    return $this;
}


#[ORM\Column(type: 'string', length: 3, nullable: true)]
private ?string $groupeSanguin = null;

public function getGroupeSanguin(): ?GroupeSanguin
{
    return $this->groupeSanguin ? GroupeSanguin::from($this->groupeSanguin) : null;
}

public function setGroupeSanguin(?GroupeSanguin $groupeSanguin): self
{
    $this->groupeSanguin = $groupeSanguin?->value;

    return $this;
}

    /**
     * @return Collection<int, RDV>
     */
    public function getRDVs(): Collection
    {
        return $this->RDVs;
    }

    public function addRDV(RDV $rDV): static
    {
        if (!$this->RDVs->contains($rDV)) {
            $this->RDVs->add($rDV);
            $rDV->setPatient($this);
        }

        return $this;
    }

    public function removeRDV(RDV $rDV): static
    {
        if ($this->RDVs->removeElement($rDV)) {
            // set the owning side to null (unless already changed)
            if ($rDV->getPatient() === $this) {
                $rDV->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueMedical>
     */
    public function getHistoriquesMedicales(): Collection
    {
        return $this->historiquesMedicales;
    }

    public function addHistoriqueMedical(HistoriqueMedical $historiquesMedicale): static
    {
        if (!$this->historiquesMedicales->contains($historiquesMedicale)) {
            $this->historiquesMedicales->add($historiquesMedicale);
            $historiquesMedicale->setPatient($this);
        }

        return $this;
    }

    public function removeHistoriqueMedical(HistoriqueMedical $historiquesMedicale): static
    {
        if ($this->historiquesMedicales->removeElement($historiquesMedicale)) {
            // set the owning side to null (unless already changed)
            if ($historiquesMedicale->getPatient() === $this) {
                $historiquesMedicale->setPatient(null);
            }
        }

        return $this;
    }

    // Méthode utilitaire pour calculer l'âge
    public function getAge(): ?int
    {
        if ($this->dateNaissance) {
            $now = new \DateTime();
            return $now->diff($this->dateNaissance)->y;
        }
        return null;
    }
}
