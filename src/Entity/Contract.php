<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idContract')]
    private ?int $idContract = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(name: 'effectiveDate', nullable: true)]
    private ?\DateTimeImmutable $effectiveDate = null;

    #[ORM\Column(name: 'terminationDate', nullable: true)]
    private ?\DateTime $terminationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $purpose = null;

    #[ORM\Column(name: 'termsAndConditions', type: Types::TEXT, nullable: true)]
    private ?string $termsAndConditions = null;

    #[ORM\Column(name: 'paymentMethod', length: 255)]
    private ?string $paymentMethod = null;

    #[ORM\Column(name: 'recivingLocation', length: 255, nullable: true)]
    private ?string $recivingLocation = null;

    #[ORM\OneToOne(mappedBy: 'contract', cascade: ['persist', 'remove'])]
    private ?Transaction $transaction = null;

    public function getId(): ?int
    {
        return $this->idContract;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getEffectiveDate(): ?\DateTimeImmutable
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(?\DateTimeImmutable $effectiveDate): static
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    public function getTerminationDate(): ?\DateTime
    {
        return $this->terminationDate;
    }

    public function setTerminationDate(?\DateTime $terminationDate): static
    {
        $this->terminationDate = $terminationDate;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): static
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getTermsAndConditions(): ?string
    {
        return $this->termsAndConditions;
    }

    public function setTermsAndConditions(?string $termsAndConditions): static
    {
        $this->termsAndConditions = $termsAndConditions;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getRecivingLocation(): ?string
    {
        return $this->recivingLocation;
    }

    public function setRecivingLocation(?string $recivingLocation): static
    {
        $this->recivingLocation = $recivingLocation;
        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        // unset the owning side of the relation if necessary
        if ($transaction === null && $this->transaction !== null) {
            $this->transaction->setContract(null);
        }

        // set the owning side of the relation if necessary
        if ($transaction !== null && $transaction->getContract() !== $this) {
            $transaction->setContract($this);
        }

        $this->transaction = $transaction;

        return $this;
    }
}
