<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=App\Repository\PhotoShootRepository::class)
 */
class PhotoShoot
{
    const STATUS_PENDING = 'pending';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"photoshoot"})
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="photoShoots")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"photoshoot"})
     */
    private User $customer;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"photoshoot"})
     */
    private \DateTime $expiration;

    /**
     * @ORM\Column(type="string", length=30)
     * @Groups({"photoshoot"})
     */
    private string $status;

    /**
     * @ORM\ManyToOne(targetEntity=PhotoPackage::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"photoshoot"})
     */
    private $package;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getExpiration(): ?\DateTimeInterface
    {
        return $this->expiration;
    }

    public function setExpiration(?\DateTimeInterface $expiration): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPackage(): ?PhotoPackage
    {
        return $this->package;
    }

    public function setPackage(?PhotoPackage $package): self
    {
        $this->package = $package;

        return $this;
    }
}
