<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use LibreSign\Behat\MailpitExtension\Context\OpenedEmailStorageAwareContext;
use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use Override;

final class OpenedEmailStorageContextInitializer implements ContextInitializer
{
    private OpenedEmailStorage $openedEmailStorage;

    public function __construct(OpenedEmailStorage $openedEmailStorage)
    {
        $this->openedEmailStorage = $openedEmailStorage;
    }

    #[Override]
    public function initializeContext(Context $context): void
    {
        if (!$context instanceof OpenedEmailStorageAwareContext) {
            return;
        }

        $context->setOpenedEmailStorage($this->openedEmailStorage);
    }
}
