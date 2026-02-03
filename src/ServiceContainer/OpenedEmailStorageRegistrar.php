<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class OpenedEmailStorageRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $openedEmailStorage = new Definition(OpenedEmailStorage::class);

        $container->setDefinition('mailpit.opened_email_storage', $openedEmailStorage);
    }
}
