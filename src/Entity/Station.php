<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idStation")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Nom Station is required")]
    #[Assert\Regex(pattern:"/^[A-Za-z,]+$/",message:"Nom Station should contain only letters")]
    #[Assert\Length(min: 5, minMessage: "Nom Station should be at least 5 characters long")]


    private ?string $nomstation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Address Station is required")]


    private ?string $addressstation = null;

    #[ORM\Column(length: 255)]
    private ?string $Type_Vehicule = null;


    #[Vich\UploadableField(mapping: 'station', fileNameProperty: 'image_station')]
    #[Assert\NotBlank(message:"Image Station is required")]

    private ?File $imageFile = null;
   
    #[ORM\Column(length: 255)]
    private ?string $image_station = null;

    public function getImageStation(): ?string
    {
        return $this->image_station;
    }

    public function setImageStation(?string $image_station): void
    {
        $this->image_station = $image_station;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnomstation(): ?string
    {
        return $this->nomstation;
    }

    public function setnomstation(string $nomstation): static
    {
        $this->nomstation = $nomstation;

        return $this;
    }

    public function getaddressstation(): ?string
    {
        return $this->addressstation;
    }

    public function setaddressstation(string $addressstation): static
    {
        $this->addressstation = $addressstation;

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
