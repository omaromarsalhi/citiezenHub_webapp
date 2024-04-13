<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idTransaction')]
    private ?int $idTransaction = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(name: "idProduct",referencedColumnName:"idProduct")]
    private ?Product $product = null;

    #[ORM\Column(nullable: true,name:'idSeller')]
    private ?int $idSeller = null;

    #[ORM\Column(nullable: true,name:'idBuyer')]
    private ?int $idBuyer = null;

    #[ORM\Column(nullable: true,name:'pricePerUnit')]
    private ?float $pricePerUnit = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(length: 255, nullable: true,name:'transactionMode')]
    private ?string $transactionMode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $timestamp = null;

    #[ORM\OneToOne(inversedBy: 'transaction', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "idContract",referencedColumnName:"idContract")]
    private ?Contract $contract = null;

    public function getId(): ?int
    {
        return $this->idTransaction;
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

    public function getIdSeller(): ?int
    {
        return $this->idSeller;
    }

    public function setIdSeller(?int $idSeller): static
    {
        $this->idSeller = $idSeller;

        return $this;
    }

    public function getIdBuyer(): ?int
    {
        return $this->idBuyer;
    }

    public function setIdBuyer(?int $idBuyer): static
    {
        $this->idBuyer = $idBuyer;

        return $this;
    }

    public function getPricePerUnit(): ?float
    {
        return $this->pricePerUnit;
    }

    public function setPricePerUnit(?float $pricePerUnit): static
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTransactionMode(): ?string
    {
        return $this->transactionMode;
    }

    public function setTransactionMode(?string $transactionMode): static
    {
        $this->transactionMode = $transactionMode;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }
}
