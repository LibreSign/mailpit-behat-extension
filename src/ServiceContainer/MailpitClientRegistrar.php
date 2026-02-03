<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class MailpitClientRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $mailpitClient = new Definition(MailpitClient::class, [
            new Reference('mailpit.http_client'),
            new Reference('mailpit.http_request_factory'),
            new Reference('mailpit.http_stream_factory'),
            '%mailpit.base_url%',
        ]);
        $mailpitClient->setPublic(true);

        $container->setDefinition('mailpit.client', $mailpitClient);
    }
}
