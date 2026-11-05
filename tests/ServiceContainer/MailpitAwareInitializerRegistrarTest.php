<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use LibreSign\Behat\MailpitExtension\ServiceContainer\MailpitAwareInitializerRegistrar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MailpitAwareInitializerRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_mailpit_aware_initializer_service(): void
    {
        $container = new ContainerBuilder();

        $registrar = new MailpitAwareInitializerRegistrar();
        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.context_initializer'));
        $definition = $container->getDefinition('mailpit.context_initializer');
        $this->assertEquals(MailpitAwareInitializer::class, $definition->getClass());
    }

    #[Test]
    public function it_should_tag_initializer_with_context_extension_tag(): void
    {
        $container = new ContainerBuilder();

        $registrar = new MailpitAwareInitializerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.context_initializer');
        $this->assertEquals(
            [['priority' => 0]],
            $definition->getTag(ContextExtension::INITIALIZER_TAG)
        );
    }

    #[Test]
    public function it_should_inject_mailpit_client_dependency(): void
    {
        $container = new ContainerBuilder();

        $registrar = new MailpitAwareInitializerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.context_initializer');
        $arguments = $definition->getArguments();

        $this->assertCount(1, $arguments);
    }
}
