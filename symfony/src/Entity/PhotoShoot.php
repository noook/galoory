<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    const STATUS_DONE = 'done';

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"photoshoot"})
     */
    private UuidInterface $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="photoShoot")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"photoshoot"})
     */
    private User $customer;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"photoshoot"})
     */
    private \DateTime $date;

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

    /**
     * @ORM\OneToMany(targetEntity=SelectedPicture::class, mappedBy="photoshoot", orphanRemoval=true)
     */
    private $selectedPictures;

    /**
     * @ORM\Column(type="text")
     * @Groups({"photoshoot"})
     */
    private $comment;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->selectedPictures = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    /**
     * @return Collection|SelectedPicture[]
     */
    public function getSelectedPictures(): Collection
    {
        return $this->selectedPictures;
    }

    public function addSelectedPicture(SelectedPicture $selectedPicture): self
    {
        if (!$this->selectedPictures->contains($selectedPicture)) {
            $this->selectedPictures[] = $selectedPicture;
            $selectedPicture->setPhotoshoot($this);
        }

        return $this;
    }

    public function removeSelectedPicture(SelectedPicture $selectedPicture): self
    {
        if ($this->selectedPictures->contains($selectedPicture)) {
            $this->selectedPictures->removeElement($selectedPicture);
            // set the owning side to null (unless already changed)
            if ($selectedPicture->getPhotoshoot() === $this) {
                $selectedPicture->setPhotoshoot(null);
            }
        }

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
