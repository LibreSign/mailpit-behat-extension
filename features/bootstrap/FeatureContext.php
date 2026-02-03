<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use LibreSign\Behat\MailpitExtension\Context\MailpitAwareContext;
use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class FeatureContext implements Context, MailpitAwareContext
{
    /**
     * @var MailpitClient
     */
    private $mailpit;

    public function setMailpit(MailpitClient $client): void
    {
        $this->mailpit = $client;
    }

    /**
     * @Given /^I send an email with subject "([^"]*)" and body "([^"]*)"$/
     * @Given /^I send an email with subject "([^"]*)" and body "([^"]*)" to "([^"]*)"$/
     */
    public function iSendAnEmailWithSubjectAndBodyTo(string $subject, string $body, string $to = 'me@myself.example'): void
    {
        $email = (new Email())
            ->from(new Address('me@myself.example', 'Myself'))
            ->to($to)
            ->subject($subject)
            ->text($body);

        $smtpDsn = $_ENV['SMTP_DSN'] ?? 'smtp://localhost:2025';
        $transport = Transport::fromDsn($smtpDsn);
        $mailer = new Mailer($transport);

        $mailer->send($email);
    }

    /**
     * @Given /^I send an email with attachment "([^"]*)"$/
     */
    public function iSendAnEmailWithAttachment(string $filename): void
    {
        $email = (new Email())
            ->from(new Address('me@myself.example', 'Myself'))
            ->to('me@myself.example')
            ->subject('Email with attachment')
            ->text('Please see attached')
            ->attach('Hello world!', $filename, 'text/plain');

        $smtpDsn = $_ENV['SMTP_DSN'] ?? 'smtp://localhost:2025';
        $transport = Transport::fromDsn($smtpDsn);
        $mailer = new Mailer($transport);

        $mailer->send($email);
    }
}
