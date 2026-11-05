<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\Context;

use LibreSign\Behat\MailpitExtension\Context\MailpitContext;
use LibreSign\Mailpit\MailpitClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MailpitContextTest extends TestCase
{
    #[Test]
    public function it_reports_the_missing_subject_when_no_message_matches(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('findMessagesSatisfying')->willReturn([]);

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No message found with subject "missing"');

        $context->iShouldSeeAnEmailWithSubjectAndBodyFromToRecipient('missing');
    }

    #[Test]
    public function it_reports_the_missing_subject_when_opening_an_email(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('findMessagesSatisfying')->willReturn([]);

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No message found with subject "missing"');

        $context->iOpenTheEmail(subject: 'missing');
    }

    #[Test]
    public function it_preserves_query_errors_from_the_mailpit_client(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('findMessagesSatisfying')
            ->willThrowException(new RuntimeException('Mailpit query failed: HTTP 503'));

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mailpit query failed: HTTP 503');

        $context->iShouldSeeAnEmailWithSubjectAndBodyFromToRecipient('test');
    }

    #[Test]
    public function it_preserves_connection_errors_while_purging_the_inbox(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('purgeMessages')
            ->willThrowException(new RuntimeException('Mailpit connection refused'));

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mailpit connection refused');

        $context->myInboxIsEmpty();
    }

    #[Test]
    public function it_preserves_connection_errors_while_counting_messages(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('getNumberOfMessages')
            ->willThrowException(new RuntimeException('Mailpit connection refused'));

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mailpit connection refused');

        $context->thereShouldBeEmailInMyInbox(1);
    }

    #[Test]
    public function it_reports_the_expected_and_actual_inbox_counts(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->method('getNumberOfMessages')->willReturn(1);

        $context = new MailpitContext();
        $context->setMailpit($client);

        $this->expectExceptionMessage('Expected 2 messages in inbox, but there were 1');

        $context->thereShouldBeEmailInMyInbox(2);
    }
}
