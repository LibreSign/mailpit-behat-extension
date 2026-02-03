<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use LibreSign\Behat\MailpitExtension\ServiceContainer\MailpitExtension;
use LibreSign\Mailpit\MailpitClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpClient\Psr18Client;

final class MailpitExtensionTest extends TestCase
{
    public const BASE_URL = 'http://localhost:10025/';

    private ContainerBuilder $container;

    public function setUp(): void
    {
        $this->container = new ContainerBuilder();
    }

    #[Test]
    public function it_should_set_the_base_url_as_a_container_parameter(): void
    {
        $this->loadExtension($this->container);

        $this->assertEquals(self::BASE_URL, $this->container->getParameter('mailpit.base_url'));
    }

    #[Test]
    public function it_should_set_a_http_client_instance_in_the_container(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(Psr18Client::class, 'mailpit.http_client');
    }

    #[Test]
    public function it_should_set_a_http_stream_factory_instance_in_the_container(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(StreamFactoryInterface::class, 'mailpit.http_stream_factory');
    }

    #[Test]
    public function it_should_set_a_http_request_factory_in_the_container(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(RequestFactoryInterface::class, 'mailpit.http_request_factory');
    }

    #[Test]
    public function it_should_set_a_mailpit_client_instance_in_the_container(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(MailpitClient::class, 'mailpit.client');
    }

    #[Test]
    public function it_should_set_initializer_with_correct_tag(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(MailpitAwareInitializer::class, 'mailpit.context_initializer');

        $definition = $this->container->getDefinition('mailpit.context_initializer');
        $this->assertEquals([['priority' => 0]], $definition->getTag(ContextExtension::INITIALIZER_TAG));
    }

    #[Test]
    public function it_should_set_and_register_purge_listener(): void
    {
        $this->loadExtension($this->container);

        $this->assertContainerHasServiceOfClass(EmailPurgeListener::class, 'mailpit.purge_listener');

        $definition = $this->container->getDefinition('mailpit.purge_listener');
        $this->assertEquals([['priority' => 0]], $definition->getTag(EventDispatcherExtension::SUBSCRIBER_TAG));
    }

    #[Test]
    public function it_should_throw_exception_when_no_base_url_supplied(): void
    {
        $node = new ArrayNodeDefinition(null);
        (new MailpitExtension())->configure($node);

        $this->expectException(InvalidConfigurationException::class);
        (new Processor())->process($node->getNode(), [[]]);
    }

    #[Test]
    public function it_should_set_default_email_purge_tag_if_none_supplied(): void
    {
        $node = new ArrayNodeDefinition(null);
        (new MailpitExtension())->configure($node);

        $configuration = (new Processor())->process($node->getNode(), [['base_url' => self::BASE_URL]]);
        $this->assertEquals('email', $configuration['purge_tag']);
    }

    #[Test]
    public function it_should_register_opened_email_storage_context_initializer(): void
    {
        $this->loadExtension($this->container);

        $this->assertTrue($this->container->hasDefinition('mailpit.opened_email_storage.context_initializer'));
        $this->assertTrue($this->container->hasDefinition('mailpit.opened_email_storage'));
    }

    private function assertContainerHasServiceOfClass(string $className, string $serviceId): void
    {
        $definition = $this->container->getDefinition($serviceId);
        $this->assertInstanceOf($className, $this->container->resolveServices($definition));
    }

    private function loadExtension(ContainerBuilder $container): void
    {
        $extension = new MailpitExtension();
        $extension->load($container, ['base_url' => self::BASE_URL, 'purge_tag' => 'email']);
    }
}
