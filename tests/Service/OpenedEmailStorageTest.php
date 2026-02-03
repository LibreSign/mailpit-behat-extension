<?php

namespace LibreSign\Behat\MailpitExtension\Tests\Service;

use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use LibreSign\Mailpit\Message\Contact;
use LibreSign\Mailpit\Message\ContactCollection;
use LibreSign\Mailpit\Message\Headers;
use LibreSign\Mailpit\Message\Message;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class OpenedEmailStorageTest extends TestCase
{
    #[Test]
    public function it_should_indicate_when_no_opened_email_has_been_set(): void
    {
        $service = new OpenedEmailStorage();

        $this->assertFalse($service->hasOpenedEmail());
    }

    #[Test]
    public function it_should_indicate_when_an_opened_email_has_been_set(): void
    {
        $service = new OpenedEmailStorage();
        $service->setOpenedEmail($this->getMessage());

        $this->assertTrue($service->hasOpenedEmail());
    }

    #[Test]
    public function it_should_throw_exception_when_asked_for_opened_email_but_none_was_set(): void
    {
        $service = new OpenedEmailStorage();

        $this->expectException(RuntimeException::class);
        $service->getOpenedEmail();
    }

    #[Test]
    public function it_should_return_the_set_opened_email(): void
    {
        $service = new OpenedEmailStorage();
        $message = $this->getMessage();

        $service->setOpenedEmail($message);
        $this->assertEquals($message, $service->getOpenedEmail());
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return new Message(
            '1234',
            new Contact('me@myself.example'),
            new ContactCollection([new Contact('me@myself.example')]),
            new ContactCollection([]),
            new ContactCollection([]),
            'Test e-mail',
            'Hello there!',
            [],
            new Headers([]),
        );
    }
}
