<?php

namespace App\Action;

use App\Google\GmailAuthenticator;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class SendMailHandler implements MessageHandlerInterface
{
    private Google_Client $client;
    private ParameterBagInterface $bag;

    public function __construct(GmailAuthenticator $authenticator, ParameterBagInterface $bag)
    {
        $this->client = $authenticator->getClient();
        $this->bag = $bag;
    }

    public function __invoke(SendMail $command)
    {
        $email = (new Email())
            ->from($this->bag->get('notified_admin'))
            ->to($command->getRecipient())
            ->addBcc($this->bag->get('notified_admin'))
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
