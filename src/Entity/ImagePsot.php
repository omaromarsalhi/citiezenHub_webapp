<?php

namespace App\Entity;

use App\Repository\ImagePsotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImagePsotRepository::class)]
#[Vich\Uploadable]
class ImagePsot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idImg')]
    private ?int $idImg = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $path = null;

    #[Vich\UploadableField(mapping: 'blog', fileNameProperty: 'path')]
    private ?File $imageFile = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: "images")]
    #[ORM\JoinColumn(name: "idPost", referencedColumnName: "id", nullable: false)]
    private $post;

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
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
}
