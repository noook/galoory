<?php
declare(strict_types=1);

namespace App\Action;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
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
            if ($errors->get(0)->getCode() === UniqueEntity::NOT_UNIQUE_ERROR) {
                throw new ConflictHttpException($errors->get(0)->getMessage());
            }
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }
        
        $hashedPassword = $this->userPasswordEncoder->encodePassword($user, $command->getPassword());
        $user->setPassword($hashedPassword);
        if ($command->isAdmin() === true) {
            $user->setRoles(array_merge($user->getRoles(), [USER::ROLE_ADMIN]));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
