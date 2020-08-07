<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user-register", methods={"POST"})
     */
    public function register()
    {
        return $this->json([
            'message' => 'You gonna create new User',
        ]);
    }
}
