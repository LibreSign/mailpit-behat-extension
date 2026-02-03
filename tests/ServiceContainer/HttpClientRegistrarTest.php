<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\ServiceContainer;

use LibreSign\Behat\MailpitExtension\ServiceContainer\HttpClientRegistrar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpClient\Psr18Client;

final class HttpClientRegistrarTest extends TestCase
{
    #[Test]
    public function it_should_register_http_client_service(): void
    {
        $container = new ContainerBuilder();
        $registrar = new HttpClientRegistrar();

        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.http_client'));
        $this->assertContainerHasServiceOfClass(Psr18Client::class, 'mailpit.http_client', $container);
    }

    #[Test]
    public function it_should_register_http_request_factory_service(): void
    {
        $container = new ContainerBuilder();
        $registrar = new HttpClientRegistrar();

        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.http_request_factory'));
        $this->assertContainerHasServiceOfClass(Psr18Client::class, 'mailpit.http_request_factory', $container);
    }

    #[Test]
    public function it_should_register_http_stream_factory_service(): void
    {
        $container = new ContainerBuilder();
        $registrar = new HttpClientRegistrar();

        $registrar->register($container);

        $this->assertTrue($container->hasDefinition('mailpit.http_stream_factory'));
        $this->assertContainerHasServiceOfClass(Psr18Client::class, 'mailpit.http_stream_factory', $container);
    }

    private function assertContainerHasServiceOfClass(string $expectedClass, string $serviceId, ContainerBuilder $container): void
    {
        $definition = $container->getDefinition($serviceId);
        $this->assertEquals($expectedClass, $definition->getClass());
    }
}
