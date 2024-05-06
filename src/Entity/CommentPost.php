<?php

namespace App\Entity;

use App\Repository\CommentPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentPostRepository::class)]
class CommentPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idComment" ,type: "integer")]
    private ?int $idComment = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $caption = null;

    #[ORM\Column(name: "dateComment", type: "datetime")]
    private ?\DateTimeInterface $dateComment = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "comments")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "id", nullable: false)]
    private $post;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "idCompte", referencedColumnName: "idUser")]
    private ?user $user = null;

    public function getIdComment(): ?int
    {
        return $this->idComment;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;
        return $this;
    }

    public function getDateComment(): ?\DateTimeInterface
    {
        return $this->dateComment;
    }

    public function setDateComment(?\DateTimeInterface $dateComment): self
    {
        $this->dateComment = $dateComment;
        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;
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

}
