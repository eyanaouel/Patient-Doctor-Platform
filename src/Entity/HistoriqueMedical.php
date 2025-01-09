<?php

namespace App\Entity;

use App\Repository\HistoriqueMedicalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueMedicalRepository::class)]
class HistoriqueMedical
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $diagnostic = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $prescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(nullable: true)]
    private ?array $documents = null;

    #[ORM\ManyToOne(inversedBy: 'HistoriquesMedicale')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDiagnostic(): ?string
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(string $diagnostic): static
    {
        $this->diagnostic = $diagnostic;

        return $this;
    }

    public function getPrescription(): ?string
    {
        return $this->prescription;
    }

    public function setPrescription(string $prescription): static
    {
        $this->prescription = $prescription;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDocuments(): ?array
    {
        return $this->documents;
    }

    public function setDocuments(?array $documents): static
    {
        $this->documents = $documents;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    //historique medical avec med 

    #[ORM\ManyToOne(inversedBy: 'historiquesMedicaux')]
#[ORM\JoinColumn(nullable: false)]
private ?Medecin $medecin = null;

public function getMedecin(): ?Medecin
{
    return $this->medecin;
}

public function setMedecin(?Medecin $medecin): static
{
    $this->medecin = $medecin;
    return $this;
}


}
