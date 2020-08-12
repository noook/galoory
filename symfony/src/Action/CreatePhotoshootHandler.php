<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\PhotoShoot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreatePhotoshootHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private string $uploadsDirectory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $bag
    )
    {
        $this->entityManager = $entityManager;
        $this->uploadsDirectory = realpath($bag->get('kernel.project_dir') . '/uploads');
    }

    public function __invoke(CreatePhotoshoot $command): PhotoShoot
    {
        $shoot = new PhotoShoot;
        $shoot
            ->setCustomer($command->getUser())
            ->setPackage($command->getPhotoPackage())
            ->setExpiration($command->getExpiration())
            ->setStatus(PhotoShoot::STATUS_PENDING);

        mkdir($this->uploadsDirectory . '/' . $shoot->getId());
        $this->entityManager->persist($shoot);
        $this->entityManager->flush();

        return $shoot;
    }
}
