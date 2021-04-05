<?php

namespace App\Action;

use App\Entity\PhotoShoot;

class EditPhotoshoot
{
    private PhotoShoot $photoshoot;
    private int $quantity;
    private array $user;
    private \DateTime $date;
    private string $comment;

    public function __construct(
        PhotoShoot $photoshoot,
        int $quantity,
        array $user,
        \DateTime $date,
        string $comment
    )
    {
        $this->photoshoot = $photoshoot;
        $this->quantity = $quantity;
        $this->user = $user;
        $this->date = $date;
        $this->comment = $comment;
    }

    public function getPhotoshoot(): PhotoShoot
    {
        return $this->photoshoot;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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