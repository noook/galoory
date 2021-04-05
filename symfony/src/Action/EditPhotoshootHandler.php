<?php

namespace App\Action;

use App\Entity\PhotoShoot;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EditPhotoshootHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository
    )
    {
        $this->em = $em;        
        $this->userRepository = $userRepository;        
    }

    public function __invoke(EditPhotoshoot $command): PhotoShoot
    {
        $photoshoot = $command->getPhotoshoot();
        $user = $photoshoot->getCustomer();
        $updatedUser = $command->getUser();

        if ($user->getEmail() !== $updatedUser['email']) {
            $userExists = $this->userRepository->findOneBy(['email' => $updatedUser['email']]) !== null;

            if ($userExists) {
                throw new ConflictHttpException(sprintf('A user with the email "%s" already exists.', $updatedUser['email']));
            }
        }

        $user
            ->setEmail($updatedUser['email'])
            ->setFirstname($updatedUser['firstname']);

        $photoshoot
            ->setDate($command->getDate())
            ->setQuantity($command->getQuantity());

        $this->em->flush();

        return $photoshoot;
    }
}
