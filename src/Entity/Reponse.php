<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "The reclamation text cannot be blank.")]
    #[Assert\Length(
        min: 5,
        minMessage: "The reclamation text must be at least {{ limit }} characters long.",
        max: 65535,
        maxMessage: "The reclamation text cannot be longer than {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9,. ]+$/',
        message: "The reclamation text must only contain letters, numbers, commas, periods, and spaces."
    )]
    private ?string $repReclamation = null;
    

    #[ORM\OneToOne(inversedBy: 'reponse', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reeclamation $reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepReclamation(): ?string
    {
        return $this->repReclamation;
    }

    public function setRepReclamation(string $repReclamation): static
    {
        $this->repReclamation = $repReclamation;

        return $this;
    }

    public function getReclamation(): ?Reeclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(Reeclamation $reclamation): static
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
