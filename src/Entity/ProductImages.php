<?php

namespace App\Entity;

use App\Repository\ProductImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductImagesRepository::class)]
class ProductImages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idImage')]
    private ?int $idImage = null;

    #[ORM\Column(name:'idProduct',nullable: true)]
    private ?int $idProduct = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(name: "idProduct",referencedColumnName:"idProduct")]
    private ?Product $product = null;

    public function getIdImage(): ?int
    {
        return $this->idImage;
    }

    public function getIdProduct(): ?int
    {
        return $this->idProduct;
    }

    public function setIdProduct(?int $idProduct): static
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }


}
