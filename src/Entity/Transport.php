<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idTransport")]
    private ?int $idTransport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TypeVehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Reference = null;

    #[ORM\Column(length: 255)]
    private ?string $Vehiculeimage = null;
    #[Vich\UploadableField(mapping: 'transport', fileNameProperty: 'Vehiculeimage')]
    private ?File $imageFile = null;

    #[ORM\Column]
    private ?float $Prix = null;

    // Heure property with string type
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Heure = null;

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
        return $this->TypeVehicule;
    }

    public function setTypeVehicule(?string $TypeVehicule): static
    {
        $this->TypeVehicule = $TypeVehicule;

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
        return $this->Vehiculeimage;
    }

    public function setVehiculeImage(string $VehiculeImage): static
    {
        $this->Vehiculeimage = $VehiculeImage;

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

    public function getHeure(): ?string
    {
        return $this->Heure;
    }

    public function setHeure(?string $Heure): static
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
    public function setImageFile($imageFile)
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
        }
    }
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
