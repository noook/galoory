<?php

namespace App\Action;

class SendMail {
    private string $subject;
    private string $body;
    private string $altBody;
    private string $recipient;

    public function __construct(
        string $subject,
        string $body,
        string $altBody,
        string $recipient
    )
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->altBody = $altBody;
        $this->recipient = $recipient;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getAltBody(): string {
        return $this->body;
    }

    public function getRecipient(): string {
        return $this->recipient;
    }
}