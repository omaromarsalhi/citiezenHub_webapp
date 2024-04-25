<?php

namespace App\Entity;

use App\Repository\AiResultRepository;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: AiResultRepository::class)]
class AiResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idAiResult')]
    private ?int $idAiResult = null;

    #[ORM\Column(name:'idProduct',nullable: true)]
    private ?int $idProduct = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(name:'terminationDate',type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $terminationDate = null;

    public function getId(): ?int
    {
        return $this->idAiResult;
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getTerminationDate(): ?\DateTimeInterface
    {
        return $this->terminationDate;
    }

    public function setTerminationDate(): static
    {
        $currentTimestamp = new DateTime();
        $currentTimestamp->setTimezone(new DateTimeZone('Africa/Tunis'));
        $currentTimestamp->modify('+2 days');
        $this->terminationDate = $currentTimestamp;
        return $this;
    }

    public function getTerminationDateDate(): string
    {
        return $this->terminationDate->format('Y-m-d');
    }
    public function getTerminationDateTime(): string
    {
        return $this->terminationDate->format('H:i:s');
    }

}
