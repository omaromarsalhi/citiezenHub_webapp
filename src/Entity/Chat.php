<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idChat')]
    private ?int $idChat = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: "idSender",referencedColumnName:"idUser")]
    private ?user $sender = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: "idReciver",referencedColumnName:"idUser")]
    private ?user $reciver = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(name:'msgState' , nullable: true)]
    private ?int $msgState = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    public function getId(): ?int
    {
        return $this->idChat;
    }

    public function getSender(): ?user
    {
        return $this->sender;
    }

    public function setSender(?user $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReciver(): ?user
    {
        return $this->reciver;
    }

    public function setReciver(?user $reciver): static
    {
        $this->reciver = $reciver;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getMsgState(): ?int
    {
        return $this->msgState;
    }

    public function setMsgState(?int $msgState): static
    {
        $this->msgState = $msgState;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
