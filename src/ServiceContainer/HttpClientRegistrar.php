<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpClient\Psr18Client;

final class HttpClientRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $httpClient = new Definition(Psr18Client::class);

        $container->setDefinition('mailpit.http_client', $httpClient);
        $container->setDefinition('mailpit.http_request_factory', $httpClient);
        $container->setDefinition('mailpit.http_stream_factory', $httpClient);
    }
}
