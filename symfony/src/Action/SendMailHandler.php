<?php

namespace App\Action;

use App\Google\GmailAuthenticator;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendMailHandler implements MessageHandlerInterface
{
    private Google_Client $client;

    public function __construct(GmailAuthenticator $authenticator)
    {
        $this->client = $authenticator->getClient();
    }

    public function __invoke(SendMail $command)
    {
        $email = (new Email())
            ->from('me@neilrichter.com')
            ->to($command->getRecipient())
            ->subject($command->getSubject())
            ->text($command->getAltBody())
            ->html($command->getBody());


        $service = new Google_Service_Gmail($this->client);
        $message = new Google_Service_Gmail_Message;
        $raw = strtr(base64_encode($email->toString()), ['+' => '-', '/' => '_']);
        $message->setRaw($raw);
        $service->users_messages->send('me', $message);
    }
}