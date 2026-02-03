<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use LibreSign\Behat\MailpitExtension\Context\Initializer\OpenedEmailStorageContextInitializer;
use LibreSign\Behat\MailpitExtension\ServiceContainer\OpenedEmailStorageContextInitializerRegistrar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OpenedEmailStorageContextInitializerRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_opened_email_storage_context_initializer_service(): void
    {
        $container = new ContainerBuilder();

        $registrar = new OpenedEmailStorageContextInitializerRegistrar();
        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.opened_email_storage.context_initializer'));
        $definition = $container->getDefinition('mailpit.opened_email_storage.context_initializer');
        $this->assertEquals(OpenedEmailStorageContextInitializer::class, $definition->getClass());
    }

    #[Test]
    public function it_should_tag_initializer_with_context_extension_tag(): void
    {
        $container = new ContainerBuilder();

        $registrar = new OpenedEmailStorageContextInitializerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.opened_email_storage.context_initializer');
        $this->assertEquals(
            [['priority' => 0]],
            $definition->getTag(ContextExtension::INITIALIZER_TAG)
        );
    }

    #[Test]
    public function it_should_inject_opened_email_storage_dependency(): void
    {
        $container = new ContainerBuilder();

        $registrar = new OpenedEmailStorageContextInitializerRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.opened_email_storage.context_initializer');
        $arguments = $definition->getArguments();

        $this->assertCount(1, $arguments);
    }
}
