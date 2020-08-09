<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ValidatorInterface $validator
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;
    }

    public function __invoke(CreateUser $command): User
    {
        $user = new User;
        $user
            ->setFirstname($command->getFirstname())
            ->setLastname($command->getLastname())
            ->setEmail($command->getEmail());

        $errors = $this->validator->validate($user);
        if ($errors->has(0)) {
            throw new BadRequestException($errors->get(0)->getMessage());
        }
        
        $hashedPassword = $this->userPasswordEncoder->encodePassword($user, $command->getPassword());
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
