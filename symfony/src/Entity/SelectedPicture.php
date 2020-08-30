<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SelectedPictureRepository::class)
 */
class SelectedPicture
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"selected-picture"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PhotoShoot::class, inversedBy="selectedPictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photoshoot;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"selected-picture"})
     */
    private $filename;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPhotoshoot(): ?PhotoShoot
    {
        return $this->photoshoot;
    }

    public function setPhotoshoot(?PhotoShoot $photoshoot): self
    {
        $this->photoshoot = $photoshoot;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
