<?php

namespace App\Action;

use App\Entity\PhotoPackage;
use App\Entity\PhotoShoot;

class EditPhotoshoot
{
    private PhotoShoot $photoshoot;
    private PhotoPackage $package;
    private array $user;
    private \DateTime $expiration;

    public function __construct(
        PhotoShoot $photoshoot,
        PhotoPackage $package,
        array $user,
        \DateTime $expiration
    )
    {
        $this->photoshoot = $photoshoot;
        $this->package = $package;
        $this->user = $user;
        $this->expiration = $expiration;
    }

    public function getPhotoshoot(): PhotoShoot
    {
        return $this->photoshoot;
    }

    public function getPhotoPackage(): PhotoPackage
    {
        return $this->package;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }
}