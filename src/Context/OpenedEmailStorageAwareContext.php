<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Context;

use Behat\Behat\Context\Context;
use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;

interface OpenedEmailStorageAwareContext extends Context
{
    public function setOpenedEmailStorage(OpenedEmailStorage $storage): void;
}
