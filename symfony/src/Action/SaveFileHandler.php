<?php
declare(strict_types=1);

namespace App\Action;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SaveFileHandler implements MessageHandlerInterface
{
    private string $uploadsDirectory;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->uploadsDirectory = realpath($bag->get('kernel.project_dir') . '/uploads');
    }
    public function __invoke(SaveFile $command)
    {
        $file = $command->getFile();
        $uploadDirectory = realpath($this->uploadsDirectory . '/' . $command->getPhotoShoot()->getId()) . '/';

        if ($file->getMimeType() === 'application/zip') {
            $zip = new \ZipArchive;

            if ($zip->open($file->getRealPath()) !== true) {
                throw new FileException(sprintf('Cannot open archive "%s"', $file->getFilename()));
            }

            for ($i = 0; $i < $zip->numFiles; $i += 1) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);

                if (in_array($fileinfo['filename'], ['.DS_Store', '.localized'])) {
                    continue;
                }

                if (strpos($fileinfo['dirname'], '__MACOSX') !== false) {
                    continue;
                }

                if ($fileinfo['basename'] === $fileinfo['filename']) {
                    continue;
                }

                copy('zip://' . $file->getRealPath() . '#' . $filename, $uploadDirectory . $fileinfo['basename']);
            }
            $zip->close();
        } else {
            $file->move($uploadDirectory, $file->getClientOriginalName());
        }
    }
}
