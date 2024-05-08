<?php

namespace App\Entity;

use App\Repository\MunicipaliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MunicipaliteRepository::class)]
class Municipalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idMunicipalite")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{3,}$/',
        message: "Le nom contient que des alphabet ",
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ladresse ne peut pas être vide')]
    #[Assert\Length(
        min:5, minMessage: 'ladresse il faut contenir au moi 5 caractere',
    )]
    private ?string $Address = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: "municipalite")]
    private Collection $users;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $goverment = null;


    public function __construct()
    {
        $this->users = new ArrayCollection();

    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->Address;
    }

    public function setAddress(string $Address): static
    {
        $this->Address = $Address;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users->toArray();
    }

    public function addUser(User $User): self
    {
        if (!$this->users->contains($User)) {
            $this->users[] = $User;
            $User->setMunicipalite($this);
        }

        return $this;
    }

    public function getGoverment(): ?string
    {
        return $this->goverment;
    }

    public function setGoverment(?string $goverment): static
    {
        $this->goverment = $goverment;

        return $this;
    }

}
