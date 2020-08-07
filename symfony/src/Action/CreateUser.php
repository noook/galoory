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

    private string $lastname;

    /**
     * @Assert\NotCompromisedPassword
     * @Assert\Length(min = 8)
     */
    private string $password;

    public function __construct(
        string $email,
        string $firstname,
        string $lastname,
        string $password
    )
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
