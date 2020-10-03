<?php

namespace App\Action;

use App\Entity\PhotoPackage;
use App\Entity\User;

class CreatePhotoshoot
{
    private User $user;
    private PhotoPackage $package;
    private \DateTime $date;
    private string $comment;
    
    public function __construct(
        User $user,
        PhotoPackage $package,
        \DateTime $date,
        string $comment
    )
    {
        $this->user = $user;
        $this->package = $package;
        $this->date = $date;
        $this->comment = $comment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPhotoPackage(): PhotoPackage
    {
        return $this->package;
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
