<?php

namespace App\Action;

use App\Entity\User;

class CreatePhotoshoot
{
    private User $user;
    private int $quantity;
    private \DateTime $date;
    private string $comment;
    
    public function __construct(
        User $user,
        int $quantity,
        \DateTime $date,
        string $comment
    )
    {
        $this->user = $user;
        $this->quantity = $quantity;
        $this->date = $date;
        $this->comment = $comment;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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
