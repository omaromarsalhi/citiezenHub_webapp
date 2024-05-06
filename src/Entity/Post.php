<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Broadcast]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $date_post = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Le champ caption ne peut pas être vide.')]
    private ?string $caption = null;

    #[ORM\Column(name:'nbReactions',type: "integer", nullable: true)]
    private ?int $nbReactions = null;


    #[ORM\OneToMany(targetEntity: ImagePsot::class, mappedBy: "post")]
    #[Assert\Count(min: 1, minMessage: 'Vous devez ajouter au moins une image.')]
    private Collection $images;

    #[ORM\OneToMany(targetEntity: CommentPost::class, mappedBy: "post")]
    private Collection $comments;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "posts")]
    #[ORM\JoinColumn(name: "compte", referencedColumnName: "idUser")]
    private $user;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getDatePost(): ?\DateTimeInterface
    {
        return $this->date_post;
    }

    public function setDatePost(?\DateTimeInterface $date_post): self
    {
        $this->date_post = $date_post;
        return $this;
    }

    public function getNbReactions(): ?int
    {
        return $this->nbReactions;
    }

    public function setNbReactions(?int $nbReactions): self
    {
        $this->nbReactions = $nbReactions;
        return $this;
    }

    /**
     * @return ImagePsot[]
     */
    public function getImages(): array
    {
        return $this->images->toArray();
    }

    public function addImage(ImagePsot $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(ImagePsot $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(CommentPost $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(CommentPost $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
