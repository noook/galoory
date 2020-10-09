<?php

namespace App\Action;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUser
{
    /**
     * @Assert\Email
     */
    private string $email;

    private string $firstname;

    private bool $admin;

    /**
     * @Assert\Length(min = 8)
     */
    private string $password;

    public function __construct(
        string $email,
        string $firstname,
        string $password,
        bool $admin = false
    )
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->password = $password;
        $this->admin = $admin;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }
}
