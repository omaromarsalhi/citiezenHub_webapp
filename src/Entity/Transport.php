<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idTransport")]
    private ?int $idTransport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Type_Vehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Reference = null;

    #[ORM\Column(length: 255)]
    private ?string $Vehicule_Image = null;

    #[ORM\Column]
    private ?float $Prix = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Heure = null;

    #[ORM\Column]
    private ?int $Station_depart = null;

    #[ORM\Column]
    private ?int $Station_arrive = null;

    public function getIdTransport(): ?int
    {
        return $this->idTransport;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->Type_Vehicule;
    }

    public function setTypeVehicule(?string $Type_Vehicule): static
    {
        $this->Type_Vehicule = $Type_Vehicule;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->Reference;
    }

    public function setReference(?string $Reference): static
    {
        $this->Reference = $Reference;

        return $this;
    }

    public function getVehiculeImage(): ?string
    {
        return $this->Vehicule_Image;
    }

    public function setVehiculeImage(string $Vehicule_Image): static
    {
        $this->Vehicule_Image = $Vehicule_Image;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): static
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->Heure;
    }

    public function setHeure(\DateTimeInterface $Heure): static
    {
        $this->Heure = $Heure;

        return $this;
    }

    public function getStationDepart(): ?int
    {
        return $this->Station_depart;
    }

    public function setStationDepart(int $Station_depart): static
    {
        $this->Station_depart = $Station_depart;

        return $this;
    }

    public function getStationArrive(): ?int
    {
        return $this->Station_arrive;
    }

    public function setStationArrive(int $Station_arrive): static
    {
        $this->Station_arrive = $Station_arrive;

        return $this;
    }
}
