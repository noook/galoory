<?php

namespace App\Command;

use App\Action\CreateUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'create:admin';
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates an admin for first use')
            ->addArgument('firstname', InputArgument::REQUIRED, 'Admin\'s firstname')
            ->addArgument('email', InputArgument::REQUIRED, 'Admin\'s email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin\'s password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $firstname = $input->getArgument('firstname');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        try {
            $envelope = $this->bus->dispatch(
                new CreateUser(
                    $email,
                    $firstname,
                    $password,
                    true
                )
            );
        } catch (\Exception $e) {
            $io->error('An error occurred while creating an admin');
            return 1;
        }

        $io->success('New admin successfully created');

        return 0;
    }
}
