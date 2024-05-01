<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_Transport = null;

    #[ORM\Column]
    private ?int $id_User = null;

    #[ORM\Column]
    private ?int $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTransport(): ?int
    {
        return $this->id_Transport;
    }

    public function setIdTransport(int $id_Transport): static
    {
        $this->id_Transport = $id_Transport;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_User;
    }

    public function setIdUser(int $id_User): static
    {
        $this->id_User = $id_User;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}
