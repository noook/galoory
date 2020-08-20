<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserGrantRolesCommand extends Command
{
    protected static $defaultName = 'user:grant:roles';

    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em
    )
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Select a list of roles to grant to a user')
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the user to which we want to grant roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            $io->error(sprintf('User with email "%s" doesn\'t exist', $email));
            return 1;
        }

        $io->note(sprintf(
            "This user is currently granted the following roles:\n[%s]",
            implode(', ', $user->getRoles())
        ));

        $question = new ChoiceQuestion(
            'Which roles should this user have ?',
            array_reduce(
                User::AVAILABLE_ROLES,
                fn ($acc, $role) => array_merge($acc, [$role => $role]), []
            )
        );
        $question->setMultiselect(true);

        $answer = $io->askQuestion($question);

        $user->setRoles($answer);
        $this->em->flush();

        $io->success(sprintf(
            "User \"%s\" is now granted the following roles:\n[%s]",
            $user->getEmail(),
            implode(', ', $user->getRoles())
        ));

        return 0;
    }
}
