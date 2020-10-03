<?php

namespace App\Action;

use App\Entity\PhotoPackage;
use App\Entity\PhotoShoot;

class EditPhotoshoot
{
    private PhotoShoot $photoshoot;
    private PhotoPackage $package;
    private array $user;
    private \DateTime $date;
    private string $comment;

    public function __construct(
        PhotoShoot $photoshoot,
        PhotoPackage $package,
        array $user,
        \DateTime $date,
        string $comment
    )
    {
        $this->photoshoot = $photoshoot;
        $this->package = $package;
        $this->user = $user;
        $this->date = $date;
        $this->comment = $comment;
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

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}