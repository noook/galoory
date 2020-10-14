<?php

namespace App\Action;

class SendMail {
    private string $subject;
    private string $body;
    private string $altBody;
    private string $recipient;
    private string $notify;

    public function __construct(
        string $subject,
        string $body,
        string $altBody,
        string $recipient,
        bool $notify = false,
    )
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->altBody = $altBody;
        $this->recipient = $recipient;
        $this->notify = $notify;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getAltBody(): string {
        return $this->altBody;
    }

    public function getRecipient(): string {
        return $this->recipient;
    }

    public function shouldNotify(): bool {
        return $this->notify;
    }
}