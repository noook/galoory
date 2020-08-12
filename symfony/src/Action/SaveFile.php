<?php

namespace App\Action;

use App\Entity\PhotoShoot;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SaveFile
{
    private UploadedFile $file;
    private PhotoShoot $photoshoot;

    public function __construct(UploadedFile $file, PhotoShoot $photoshoot)
    {
        $this->file = $file;
        $this->photoshoot = $photoshoot;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getPhotoShoot(): PhotoShoot
    {
        return $this->photoshoot;
    }
}
