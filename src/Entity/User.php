<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

//#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements PasswordAuthenticatedUserInterface,UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idUser")]
    private ?int $idUser = null;

    #[ORM\Column(name:"firstName",length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{3,}$/',
        message: "Le nom contient que des alphabet ",
        groups: ['creation'],
)]
    private ?string $firstName = null;

    #[ORM\Column(name:"lastName",length: 255)]
    #[Assert\NotBlank(message: 'Le lastname ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]{3,}$/',
        message: "Le nom contient que des alphabet ",
        groups: ['creation'],
)]
    private ?string $lastName = null;

    #[ORM\Column(name:"cin",length: 255)]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}$/',
        message: "Le cin contient que des numeros ",
        groups: ['creation'],
    )]
    private ?string $cin = null;
//    /**
//      @Assert\NotBlank(message="Le prénom ne peut pas être vide.")
//      @Assert\Email
//     */
    #[ORM\Column(name:"email",length: 255)]
    #[Assert\NotBlank(message: 'Lemail ne pas etre vide ')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        message: "To ensure that your email address is valid, it must be in the following format:user@example.com"
    )]
    private ?string $email = null;

    #[ORM\Column(name:"age")]
    #[Assert\GreaterThanOrEqual(
        value: 18,
        message: "age should superieur of 18",
        groups: ['creation'],

    )]
    private ?int $age = null;


    #[Assert\NotBlank(message: 'phone number ')]
    #[Assert\Regex(
        pattern: '/^\d{1,8}$/',
        message: "Le numéro de téléphone doit contenir entre 1 et 8 chiffres et aucun autre caractère.",
groups: ['creation'],

    )]
//    #[Assert\Email(groups: ['creation'])]
    #[ORM\Column(name:"phoneNumber")]
    private ?int $phoneNumber = null;


    #[Assert\NotBlank(message: 'Ladresse ne peut pas être vide')]
    #[Assert\Length(
        min:5, minMessage: 'ladresse il faut contenir au moi 5 caractere',
    )]
    #[ORM\Column(name:"address",length: 255)]
    private ?string $address = null;

    #[ORM\Column(name:"role",length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le password ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        message: "Le mot de passe doit contenir au moins une lettre minuscule, une lettre majuscule, un chiffre et un caractère spécial, avec une longueur minimale de 8 caractères",
        groups: ['editPassword'],
    )]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dob = null;

    #[ORM\Column(name:"status",length: 255)]
    private ?string $status = null;

    #[ORM\Column(name:"image",length: 255)]
    private ?string $image = null;

//    #[Vich\UploadableField(mapping: 'users', fileNameProperty: 'image')]
//    #@Ignore()
//    private ?File $imageFile = null;

    #[ORM\Column(name:"gender",length: 255, nullable: true)]
    private ?string $gender = null;
    #[ORM\ManyToOne(targetEntity: Municipalite::class, inversedBy: "users")]
    #[ORM\JoinColumn(name: "idMunicipalite ", referencedColumnName: "idMunicipalite", nullable: false)]
    private $municipalite ;

    public function getMunicipalite(): ?Municipalite
    {
        return $this->municipalite;
    }

    public function setMunicipalite(?Municipalite $municipalite): self
    {
        $this->municipalite = $municipalite;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->idUser;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): static
    {
        $this->dob = $dob;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }
//

//    public function setImageFile($imageFile)
//    {
//        $this->imageFile = $imageFile;
//        if ($imageFile) {
//            // otherwise the event listeners won't be called and the file is lost
//            $this->updatedAt = new \DateTimeImmutable();
//        }
//
//        return $this;
//
//    }

    public function getRoles()
    {
//         return $this->role;

//        // Si vous n'avez pas de champ roles, vous pouvez simplement retourner un tableau vide
        return [];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
    public function getUserIdentifier(): string
    {

        return $this->email;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }




}
