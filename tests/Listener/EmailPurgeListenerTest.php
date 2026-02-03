<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\Listener;

use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;
use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\Suite\GenericSuite;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use LibreSign\Mailpit\MailpitClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

final class EmailPurgeListenerTest extends TestCase
{
    private MailpitClient&MockObject $client;
    private EmailPurgeListener $listener;
    private EventDispatcher $dispatcher;

    public function setUp(): void
    {
        $this->client = $this->createMock(MailpitClient::class);
        $this->listener = new EmailPurgeListener($this->client, 'email');

        $this->dispatcher = new EventDispatcher(new SymfonyEventDispatcher());
        $this->dispatcher->addSubscriber($this->listener);
    }

    #[Test]
    public function it_should_purge_all_messages_before_each_scenario_in_feature_with_email_tag(): void
    {
        $this->client->expects($this->once())->method('purgeMessages');

        $scenario = new ScenarioNode('test', [], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', ['email'], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ScenarioTested::BEFORE);
    }

    #[Test]
    public function it_should_purge_all_messages_before_each_scenario_in_with_email_tag(): void
    {
        $this->client->expects($this->once())->method('purgeMessages');

        $scenario = new ScenarioNode('test', ['email'], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', [], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ScenarioTested::BEFORE);
    }

    #[Test]
    public function it_should_purge_all_messages_before_each_example_in_feature_with_email_tag(): void
    {
        $this->client->expects($this->once())->method('purgeMessages');

        $scenario = new ScenarioNode('test', [], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', ['email'], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ExampleTested::BEFORE);
    }

    #[Test]
    public function it_should_purge_all_messages_before_each_example_with_email_tag(): void
    {
        $this->client->expects($this->once())->method('purgeMessages');

        $scenario = new ScenarioNode('test', ['email'], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', [], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ExampleTested::BEFORE);
    }

    #[Test]
    public function it_should_purge_messages_only_once_on_multiple_email_tags(): void
    {
        $this->client->expects($this->once())->method('purgeMessages');

        $scenario = new ScenarioNode('test', ['email', 'email'], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', ['email', 'email'], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ExampleTested::BEFORE);
    }

    #[Test]
    public function it_should_not_purge_all_messages_before_each_scenario_without_email_tag(): void
    {
        $this->client->expects($this->never())->method('purgeMessages');

        $scenario = new ScenarioNode('test', [], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', [], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ScenarioTested::BEFORE);
    }

    #[Test]
    public function it_should_not_purge_all_messages_before_each_example_without_email_tag(): void
    {
        $this->client->expects($this->never())->method('purgeMessages');

        $scenario = new ScenarioNode('test', [], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', [], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $this->dispatcher->dispatch($event, ExampleTested::BEFORE);
    }

    #[Test]
    public function it_should_use_custom_tag_to_purge_emails(): void
    {
        $client = $this->createMock(MailpitClient::class);
        $client->expects($this->once())->method('purgeMessages');

        $listener = new EmailPurgeListener($client, 'foobarbazban');

        $dispatcher = new EventDispatcher(new SymfonyEventDispatcher());
        $dispatcher->addSubscriber($listener);

        $scenario = new ScenarioNode('test', [], [], 'test', 1);
        $event = new BeforeScenarioTested(
            new StaticEnvironment(new GenericSuite('generic', [])),
            new FeatureNode('test', 'test', ['foobarbazban'], null, [$scenario], 'test', 'en_GB', null, 1),
            $scenario
        );

        $dispatcher->dispatch($event, ScenarioTested::BEFORE);
    }
}
