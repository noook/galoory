<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=App\Repository\PhotoPackageRepository::class)
 */
class PhotoPackage
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"photo-package"})
     */
    private ?UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"photo-package"})
     */
    private string $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"photo-package"})
     */
    private int $quantity;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
