<?php 


namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedecinRepository::class)]
class Medecin extends User
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $specialite = null;

    #[ORM\Column(type: Types::JSON)]
    private array $disponibilite = [];

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $numINPLM = null;

    /**
     * @var Collection<int, RDV>
     */
    #[ORM\OneToMany(targetEntity: RDV::class, mappedBy: 'medecin')]
    private Collection $rdvs;

    /**
     * @var Collection<int, HistoriqueMedical>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueMedical::class, mappedBy: 'medecin')]
    private Collection $historiquesMedicales;

    public function __construct()
    {
        //parent::__construct(); // Appel du constructeur parent
        $this->rdvs = new ArrayCollection();
        $this->historiquesMedicales = new ArrayCollection();
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getDisponibilite(): array
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(array $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getNumINPLM(): ?string
    {
        return $this->numINPLM;
    }

    public function setNumINPLM(string $numINPLM): static
    {
        $this->numINPLM = $numINPLM;
        return $this;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numeroPro = null;
    public function getnumeroPro(): ?string
    {
        return $this->numeroPro;
    }

    public function setnumeroPro(?string $numeroPro): static
    {
        $this->numeroPro = $numeroPro;
        return $this;
    }


    // ... le reste de vos méthodes pour RDV et HistoriqueMedical ...

    
    /**
     * @return Collection<int, RDV>
     */
    public function getRdvs(): Collection
    {
        return $this->rdvs;
    }

    public function addRdv(RDV $rdv): static
    {
        if (!$this->rdvs->contains($rdv)) {
            $this->rdvs->add($rdv);
            $rdv->setMedecin($this);
        }

        return $this;
    }

    public function removeRdv(RDV $rdv): static
    {
        if ($this->rdvs->removeElement($rdv)) {
            // set the owning side to null (unless already changed)
            if ($rdv->getMedecin() === $this) {
                $rdv->setMedecin(null);
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

    public function addHistoriqueMedicale(HistoriqueMedical $historiqueMedicale): static
    {
        if (!$this->historiquesMedicales->contains($historiqueMedicale)) {
            $this->historiquesMedicales->add($historiqueMedicale);
            $historiqueMedicale->setMedecin($this);
        }

        return $this;
    }

    public function removeHistoriqueMedicale(HistoriqueMedical $historiqueMedicale): static
    {
        if ($this->historiquesMedicales->removeElement($historiqueMedicale)) {
            // set the owning side to null (unless already changed)
            if ($historiqueMedicale->getMedecin() === $this) {
                $historiqueMedicale->setMedecin(null);
            }
        }

        return $this;
    }
}