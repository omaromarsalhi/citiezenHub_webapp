<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idStation")]
    private ?int $idStation = null;

    #[ORM\Column(length: 255)]
    private ?string $NomStation = null;

    #[ORM\Column(length: 255)]
    private ?string $AddressStation = null;

    #[ORM\Column(length: 255)]
    private ?string $Type_Vehicule = null;

    #[ORM\Column(length: 255)]
    private ?string $Image_Station = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomStation(): ?string
    {
        return $this->NomStation;
    }

    public function setNomStation(string $NomStation): static
    {
        $this->NomStation = $NomStation;

        return $this;
    }

    public function getAddressStation(): ?string
    {
        return $this->AddressStation;
    }

    public function setAddressStation(string $AddressStation): static
    {
        $this->AddressStation = $AddressStation;

        return $this;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->Type_Vehicule;
    }

    public function setTypeVehicule(string $Type_Vehicule): static
    {
        $this->Type_Vehicule = $Type_Vehicule;

        return $this;
    }

    public function getImageStation(): ?string
    {
        return $this->Image_Station;
    }

    public function setImageStation(string $Image_Station): static
    {
        $this->Image_Station = $Image_Station;

        return $this;
    }
}
