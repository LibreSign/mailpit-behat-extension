<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use LibreSign\Behat\MailpitExtension\ServiceContainer\MailpitClientRegistrar;
use LibreSign\Mailpit\MailpitClient;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MailpitClientRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_mailpit_client_service(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.base_url', 'http://localhost:10025/');

        $registrar = new MailpitClientRegistrar();
        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.client'));
        $definition = $container->getDefinition('mailpit.client');
        $this->assertEquals(MailpitClient::class, $definition->getClass());
    }

    #[Test]
    public function it_should_set_mailpit_client_as_public_service(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.base_url', 'http://localhost:10025/');

        $registrar = new MailpitClientRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.client');
        $this->assertTrue($definition->isPublic());
    }

    #[Test]
    public function it_should_inject_http_client_dependencies(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('mailpit.base_url', 'http://localhost:10025/');

        $registrar = new MailpitClientRegistrar();
        $registrar->register($container);

        $definition = $container->getDefinition('mailpit.client');
        $arguments = $definition->getArguments();

        $this->assertCount(4, $arguments);
        $this->assertTrue(isset($arguments[0]));
        $this->assertTrue(isset($arguments[1]));
        $this->assertTrue(isset($arguments[2]));
        $this->assertTrue(isset($arguments[3]));
    }
}
