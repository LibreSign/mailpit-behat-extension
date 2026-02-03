<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Context;

use Behat\Behat\Context\Context;
use LibreSign\Mailpit\MailpitClient;

interface MailpitAwareContext extends Context
{
    public function setMailpit(MailpitClient $client): void;
}
