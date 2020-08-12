<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\PhotoShoot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreatePhotoshootHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreatePhotoshoot $command): PhotoShoot
    {
        $shoot = new PhotoShoot;
        $shoot
            ->setCustomer($command->getUser())
            ->setPackage($command->getPhotoPackage())
            ->setExpiration($command->getExpiration())
            ->setStatus(PhotoShoot::STATUS_PENDING);

        $this->entityManager->persist($shoot);
        $this->entityManager->flush();

        return $shoot;
    }
}
