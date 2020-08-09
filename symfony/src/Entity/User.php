<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=App\Repository\UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, errorPath="email", message="Email already in use")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"user"})
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user"})
     * @Assert\Email
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user"})
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity=PhotoShoot::class, mappedBy="customer", orphanRemoval=true)
     */
    private Collection $photoShoots;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"user"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"user"})
     */
    private $lastname;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->photoShoots = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email');
        }

        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|PhotoShoot[]
     */
    public function getPhotoShoots(): Collection
    {
        return $this->photoShoots;
    }

    public function addPhotoShoot(PhotoShoot $photoShoot): self
    {
        if (!$this->photoShoots->contains($photoShoot)) {
            $this->photoShoots[] = $photoShoot;
            $photoShoot->setCustomer($this);
        }

        return $this;
    }

    public function removePhotoShoot(PhotoShoot $photoShoot): self
    {
        if ($this->photoShoots->contains($photoShoot)) {
            $this->photoShoots->removeElement($photoShoot);
            // set the owning side to null (unless already changed)
            if ($photoShoot->getCustomer() === $this) {
                $photoShoot->setCustomer(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
