<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReeclamationRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ReeclamationRepository::class)]
#[Vich\Uploadable]
class Reeclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "The private key cannot be null.")]
    #[Assert\Positive(message: "The private key must be positive.")]
    #[Assert\GreaterThanOrEqual(
        value: 1000,
        message: "The private key must be at least 4 digits long."
    )]
    private ?int $privateKey = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The description cannot be blank.")]
    #[Assert\Length(
        min: 4,
        minMessage: "The description must be at least {{ limit }} characters long."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s,.]+$/',
        message: "The description must only contain letters, commas, periods, and spaces."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[Vich\UploadableField(mapping: 'user_images', fileNameProperty: 'imagePath' ,)]
    private ?File $imageFile = null;


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        return $this;
    }
     
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The subject cannot be blank.")]
    #[Assert\Length(
        min: 4,
        minMessage: "The subject must be at least {{ limit }} characters long."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: "The subject must only contain letters."
    )]
    private ?string $subject = null;
    

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'reclamation', cascade: ['persist', 'remove'])]
    private ?Reponse $reponse = null;

    public function __construct()
    {
        $this->createdAt=new \DateTimeImmutable;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrivateKey(): ?int
    {
        return $this->privateKey;
    }

    public function setPrivateKey(int $privateKey): static
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReponse(): ?Reponse
    {
        return $this->reponse;
    }

    public function setReponse(Reponse $reponse): static
    {
        // set the owning side of the relation if necessary
        if ($reponse->getReclamation() !== $this) {
            $reponse->setReclamation($this);
        }

        $this->reponse = $reponse;

        return $this;
    }
    
}
