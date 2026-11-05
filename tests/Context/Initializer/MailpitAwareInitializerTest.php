<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\Suite\GenericSuite;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use LibreSign\Behat\MailpitExtension\Context\MailpitAwareContext;
use LibreSign\Behat\MailpitExtension\Context\MailpitContext;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use LibreSign\Mailpit\MailpitClient;
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

        $mailpitClient = $this->createMock(MailpitClient::class);

        $initializer = new MailpitAwareInitializer($mailpitClient);
        $initializer->initializeContext($context);

        $this->assertSame($mailpitClient, $context->mailpitClient);
    }

    #[Test]
    public function it_should_inject_the_listener_for_the_behat_four_scenario_hook(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->expects($this->once())->method('purgeMessages');

        $context = new MailpitContext();
        $listener = new EmailPurgeListener($client, 'email');
        (new MailpitAwareInitializer($client, $listener))->initializeContext($context);

        $scenario = new ScenarioNode('test', ['email'], [], 'test', 1);
        $feature = new FeatureNode('test', 'test', [], null, [$scenario], 'test', 'en_GB', null, 1);
        $scope = new BeforeScenarioScope(
            new StaticEnvironment(new GenericSuite('generic', [])),
            $feature,
            $scenario
        );

        $context->purgeBeforeScenario($scope);
        $context->purgeBeforeScenario($scope);
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

        $mailpitClient = $this->createMock(MailpitClient::class);

        $initializer = new MailpitAwareInitializer($mailpitClient);
        $initializer->initializeContext($context);

        $this->assertNull($context->mailpitClient);
    }
}
