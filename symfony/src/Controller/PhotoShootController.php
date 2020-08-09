<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhotoShootController extends AbstractController
{
    /**
     * @Route("/photoshoot", name="photo_shoot")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PhotoShootController.php',
        ]);
    }
}
