# Mailpit Behat Extension

A [Mailpit](https://github.com/axllent/mailpit) integration for **Behat 4** and PHP 8.2 or newer.
Use it to assert that email was delivered, inspect message bodies and attachments, and clear
Mailpit inboxes between test scenarios.

## Installation

Install this extension in the project that runs Behat. A stable Composer release
supporting Behat 4 must be published before consuming this major version without
a development branch constraint.

```bash
composer require --dev libresign/mailpit-behat-extension
```

The package supplies a PSR-18 HTTP client through Symfony HttpClient and uses
[libresign/mailpit-client](https://github.com/LibreSign/mailpit-client) for the Mailpit API.
It requires Behat `^4.0` at runtime and does not support Behat 3.

## Behat 4 configuration

Behat 4 uses PHP configuration, not YAML. In `behat.php` or `behat.dist.php`:

```php
<?php

use Behat\Config\Config;
use Behat\Config\Extension as ExtensionConfig;
use Behat\Config\Profile;
use Behat\Config\Suite;
use LibreSign\Behat\MailpitExtension\Context\MailpitContext;
use LibreSign\Behat\MailpitExtension\ServiceContainer\MailpitExtension;

return (new Config())->withProfile(
    (new Profile('default'))
        ->withSuite(
            (new Suite('default'))
                ->withPaths('%paths.base%/features')
                ->withContexts(FeatureContext::class, MailpitContext::class)
        )
        ->withExtension(new ExtensionConfig(MailpitExtension::class, [
            'base_url' => getenv('MAILPIT_BASE_URL') ?: 'http://localhost:9025',
            'purge_tag' => 'email',
        ]))
);
```

`base_url` is the HTTP URL for the Mailpit API/UI, not its SMTP endpoint, and is required
by the extension. `purge_tag` is optional and defaults to `email`. Tag a feature or
scenario with `@email` to purge messages **before** it runs. The tag also applies
to examples in a scenario outline.

The full class name `MailpitExtension::class` must be used when registering the
extension. Behat 4 no longer accepts short extension names.

## Built-in steps

The `MailpitContext` provides the existing email assertions and commands:

```gherkin
Given my inbox is empty
Then there should be 0 emails in my inbox
Then I should see an email with subject "Hello"
Then I should see an email with body "Welcome"
Then I should see an email from "sender@example.org"
Then I should see an email to "recipient@example.org"
Then I should see an email with subject "Hello" and body "Welcome"
Then I should see "Welcome" in email
Then I should see an email with attachment "invoice.pdf"

When I open the latest email with subject "Hello"
Then I should see "Welcome" in the opened email
Then I should see an attachment with filename "invoice.pdf" in the opened email
```

Subject, body, sender, and recipient filters can also be combined. The existing
step wording and regex matching are preserved in Behat 4 through PHP attributes.
Email lookup failures include the requested filters; Mailpit API and connection
exceptions are propagated so their diagnostic messages remain visible.

## Access from your own contexts

Implement `MailpitAwareContext` to receive the Mailpit API client:

```php
use LibreSign\Behat\MailpitExtension\Context\MailpitAwareContext;
use LibreSign\Mailpit\MailpitClient;

final class FeatureContext implements MailpitAwareContext
{
    private MailpitClient $mailpit;

    public function setMailpit(MailpitClient $client): void
    {
        $this->mailpit = $client;
    }
}
```

Implement `OpenedEmailStorageAwareContext` to receive the shared
`OpenedEmailStorage`, which exposes `hasOpenedEmail()` and `getOpenedEmail()`.
The `MailpitContext` also implements both interfaces.

## Testing

The GitHub Actions Behat job runs against an isolated Mailpit service for PHP
8.2 and 8.3, with HTTP mapped to `localhost:9025` and SMTP to `localhost:2025`.
To run the integration tests locally, start a disposable Mailpit instance with
the same ports and run:

```bash
composer install
MAILPIT_BASE_URL=http://localhost:9025 SMTP_DSN=smtp://localhost:2025 composer run test:behat
composer run test:unit
composer run lint
composer run cs:check
composer run phpstan
composer run psalm
composer run phpmd
```

The integration scenarios send and delete test emails. Do not point them at a
shared or production Mailpit instance.
