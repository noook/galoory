<?php

namespace App\Entity;

use App\Repository\SelectedPictureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SelectedPictureRepository::class)
 */
class SelectedPicture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PhotoShoot::class, inversedBy="selectedPictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photoshoot;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    public function getId(): ?int
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
