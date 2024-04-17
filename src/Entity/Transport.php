<?php

namespace App\Entity;

use App\Repository\TransportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: TransportRepository::class)]
class Transport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idTransport")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TypeVehicule = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"Refrence  is required")]
    #[Assert\Regex(pattern:"/^[A-Za-z]+$/",message:"Nom Station should contain only letters")]
    private ?string $Reference = null;

    #[ORM\Column(length: 255, name: "vehicule_image")]
    private ?string $vehiculeimage = null;
    
    #[Vich\UploadableField(mapping: 'transport', fileNameProperty: 'vehiculeimage')]
    #[Assert\NotBlank(message:"Image can't be null ")]

    private ?File $imageFile = null;

    #[ORM\Column]
    #[Assert\Regex(pattern:"/^(\d+(\.\d+)?|\.\d+)$/",message:"Prix Transport should contain only numbers and points")]
    #[Assert\NotBlank(message:"Price is required")]
    private ?float $Prix = 0;

    // Heure property with string type
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Heure = null;

    #[ORM\Column]
    private ?int $Station_depart = null;

    #[ORM\Column]
    private ?int $Station_arrive = null;

    public function getIdTransport(): ?int
    {
        return $this->id;
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
        return $this->vehiculeimage;
    }

    public function setVehiculeImage(string $VehiculeImage): static
    {
        $this->vehiculeimage = $VehiculeImage;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): static
    {
        if($Prix!=null)
        $this->Prix = $Prix;
    else
    $Prix=0;

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
