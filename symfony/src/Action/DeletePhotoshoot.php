<?php

namespace App\Action;

use App\Entity\PhotoShoot;

class DeletePhotoshoot
{
    private PhotoShoot $photoshoot;

    public function __construct(PhotoShoot $photoshoot)
    {
        $this->photoshoot = $photoshoot;
    }

    public function getPhotoshoot(): PhotoShoot
    {
        return $this->photoshoot;
    }
}
