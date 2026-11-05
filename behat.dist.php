<?php
declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension as ExtensionConfig;
use Behat\Config\Profile;
use Behat\Config\Suite;
use LibreSign\Behat\MailpitExtension\Context\MailpitContext;
use LibreSign\Behat\MailpitExtension\ServiceContainer\MailpitExtension;

$baseUrl = ($_ENV['MAILPIT_BASE_URL'] ?? getenv('MAILPIT_BASE_URL')) ?: 'http://localhost:9025';

return (new Config())->withProfile(
    (new Profile('default'))
        ->withSuite(
            (new Suite('default'))
                ->withPaths('%paths.base%/features')
                ->withContexts(FeatureContext::class, MailpitContext::class)
        )
        ->withExtension(new ExtensionConfig(MailpitExtension::class, [
            'base_url' => $baseUrl,
        ]))
);
