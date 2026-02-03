<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use LibreSign\Behat\MailpitExtension\ServiceContainer\EmailPurgeListenerRegistrar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class EmailPurgeListenerRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_email_purge_listener_service(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.purge_tag', 'email');

        $registrar = new EmailPurgeListenerRegistrar();
        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.purge_listener'));
        $definition = $container->getDefinition('mailpit.purge_listener');
        $this->assertEquals(EmailPurgeListener::class, $definition->getClass());
    }

    #[Test]
    public function it_should_tag_listener_with_event_dispatcher_subscriber_tag(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.purge_tag', 'email');

        $registrar = new EmailPurgeListenerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.purge_listener');
        $this->assertEquals(
            [['priority' => 0]],
            $definition->getTag(EventDispatcherExtension::SUBSCRIBER_TAG)
        );
    }

    #[Test]
    public function it_should_inject_mailpit_client_and_purge_tag_dependencies(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.purge_tag', 'email');

        $registrar = new EmailPurgeListenerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.purge_listener');
        $arguments = $definition->getArguments();

        $this->assertCount(2, $arguments);
    }
}
