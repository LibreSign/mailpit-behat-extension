<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use LibreSign\Behat\MailpitExtension\ServiceContainer\OpenedEmailStorageRegistrar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OpenedEmailStorageRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_opened_email_storage_service(): void
    {
        $container = new ContainerBuilder();

        $registrar = new OpenedEmailStorageRegistrar();
        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.opened_email_storage'));
        $definition = $container->getDefinition('mailpit.opened_email_storage');
        $this->assertEquals(OpenedEmailStorage::class, $definition->getClass());
    }

    #[Test]
    public function it_should_not_inject_dependencies_into_opened_email_storage(): void
    {
        $container = new ContainerBuilder();

        $registrar = new OpenedEmailStorageRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.opened_email_storage');
        $arguments = $definition->getArguments();

        $this->assertCount(0, $arguments);
    }
}
