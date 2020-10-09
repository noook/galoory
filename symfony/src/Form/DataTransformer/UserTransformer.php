<?php

namespace App\Form\DataTransformer;

use App\Action\CreateUser;
use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class UserTransformer implements DataTransformerInterface
{
    private UserRepository $userRepository;
    private MessageBusInterface $commandBus;

    public function __construct(
        UserRepository $userRepository,
        MessageBusInterface $commandBus
    )
    {
        $this->userRepository = $userRepository;
        $this->commandBus = $commandBus;
    }

    public function transform($value): User
    {
        if (is_string($value) && Uuid::isValid($value)) {
            $user = $this->userRepository->find($value);

            if (null === $user) {
                throw new TransformationFailedException(sprintf('User with id %s does not exist.', $value));
            }

            return $user;
        }

        if (!isset($value['email']) || !isset($value['firstname'])) {
            throw new TransformationFailedException(sprintf('Invalid payload', $value));
        }

        try {
            $envelope = $this->commandBus->dispatch(
                new CreateUser(
                    $value['email'],
                    $value['firstname'],
                    'password',
                )
            );
        } catch (\Throwable $e) {
            // throw $e->getPrevious();
            throw new TransformationFailedException($e->getMessage());
        }

        return $envelope->last(HandledStamp::class)->getResult();
    }

    public function reverseTransform($value): User
    {
        return $this->userRepository->find($value);
    }
}