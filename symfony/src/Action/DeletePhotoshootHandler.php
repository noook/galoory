<?php

namespace App\Action;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeletePhotoshootHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Filesystem $fs;
    private string $uploadsDir;

    public function __construct(
        EntityManagerInterface $em,
        Filesystem $fs,
        ParameterBagInterface $bag
    )
    {
        $this->em = $em;
        $this->fs = $fs;
        $this->uploadsDir = realpath($bag->get('uploads_dir'));
    }

    public function __invoke(DeletePhotoshoot $command)
    {
        $photoshoot = $command->getPhotoshoot();

        try {
            $this->em->remove($photoshoot->getCustomer());
            $this->fs->remove(realpath($this->uploadsDir . '/' . $photoshoot->getId()));
            $this->em->flush();
        } catch (\Exception $e) {
            throw new HttpException(500, 'An error occurred while deleting this photoshoot.');
        }
    }
}