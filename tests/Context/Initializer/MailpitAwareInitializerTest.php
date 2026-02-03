<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\Context\Initializer;

use Behat\Behat\Context\Context;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use LibreSign\Behat\MailpitExtension\Context\MailpitAwareContext;
use LibreSign\Mailpit\MailpitClient;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MailpitAwareInitializerTest extends TestCase
{
    #[Test]
    public function it_should_inject_mailpit_client_in_a_mailpit_aware_context(): void
    {
        $context = new class implements Context, MailpitAwareContext {
            /** @var MailpitClient $mailpitClient */
            public $mailpitClient;
            public function setMailpit(MailpitClient $client): void
            {
                $this->mailpitClient = $client;
            }
        };

        /** @var MockInterface|MailpitClient $mailpitClient */
        $mailpitClient = Mockery::mock(MailpitClient::class);

        $initializer = new MailpitAwareInitializer($mailpitClient);
        $initializer->initializeContext($context);

        $this->assertSame($mailpitClient, $context->mailpitClient);
    }

    #[Test]
    public function it_should_ignore_non_mailpit_aware_contexts(): void
    {
        $context = new class implements Context {
            /** @var MailpitClient $mailpitClient */
            public $mailpitClient;
            public function setMailpit(MailpitClient $client): void
            {
                $this->mailpitClient = $client;
            }
        };

        /** @var MockInterface|MailpitClient $mailpitClient */
        $mailpitClient = Mockery::mock(MailpitClient::class);

        $initializer = new MailpitAwareInitializer($mailpitClient);
        $initializer->initializeContext($context);

        $this->assertNull($context->mailpitClient);
    }
}
