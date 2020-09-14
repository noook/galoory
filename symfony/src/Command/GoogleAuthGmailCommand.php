<?php

namespace App\Command;

use App\Google\GmailAuthenticator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GoogleAuthGmailCommand extends Command
{
    protected static $defaultName = 'google:auth:gmail';

    private GmailAuthenticator $authenticator;

    public function __construct(GmailAuthenticator $authenticator)
    {
        parent::__construct();
        $this->authenticator = $authenticator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->authenticator->getClient();

        $io->success('Succesfully created the Gmail authentication token');

        return 0;
    }
}
