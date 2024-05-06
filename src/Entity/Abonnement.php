<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;

use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idAbonnement")]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Your image is required")]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $datefin = null;
    
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeAbonnement = null;
    
   

    #[Vich\UploadableField(mapping: 'abonnement', fileNameProperty: 'image')]
    private ?File $imageFile = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
    
    public function getdatefin(): ?string
    {
        return $this->datefin;
    }
/*
    public function setdatefin(?\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }
  */  

    public function getTypeAbonnement(): ?string
    {
        return $this->TypeAbonnement;
    }

    public function setTypeAbonnement(string $TypeAbonnement): static
    {
        $this->TypeAbonnement = $TypeAbonnement;

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
