<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use LibreSign\Behat\MailpitExtension\Context\MailpitAwareContext;
use LibreSign\Mailpit\MailpitClient;
use Override;

final class MailpitAwareInitializer implements ContextInitializer
{
    private MailpitClient $client;

    public function __construct(MailpitClient $client)
    {
        $this->client = $client;
    }

    #[Override]
    public function initializeContext(Context $context): void
    {
        if (!$context instanceof MailpitAwareContext) {
            return;
        }

        $context->setMailpit($this->client);
    }
}
