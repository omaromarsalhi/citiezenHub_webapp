<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
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
