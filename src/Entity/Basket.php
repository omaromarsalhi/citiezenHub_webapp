<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idBasket')]
    private ?int $idBasket = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: "idProduct",referencedColumnName:"idProduct")]
    private ?product $product = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: "idUser",referencedColumnName:"idUser")]
    private ?user $user = null;

    #[ORM\Column]
    private ?int $quantity = null;


    public function getId(): ?int
    {
        return $this->idBasket;
    }

    public function getProduct(): ?product
    {
        return $this->product;
    }

    public function setProduct(?product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

}
