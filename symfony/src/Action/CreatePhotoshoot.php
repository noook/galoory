<?php

namespace App\Action;

use App\Entity\PhotoPackage;
use App\Entity\User;

class CreatePhotoshoot
{
    private User $user;
    private PhotoPackage $package;
    private \DateTime $expiration;

    public function __construct(
        User $user,
        PhotoPackage $package,
        \DateTime $expiration
    )
    {
        $this->user = $user;
        $this->package = $package;
        $this->expiration = $expiration;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPhotoPackage(): PhotoPackage
    {
        return $this->package;
    }

    public function getExpiration(): \DateTime
    {
        return $this->expiration;
    }
}
