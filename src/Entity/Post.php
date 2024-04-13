<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;


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
    private ?string $caption = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $nbReactions = null;

    #[ORM\OneToMany(targetEntity: ImagePsot::class, mappedBy: "post")]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
}
