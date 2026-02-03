<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use LibreSign\Behat\MailpitExtension\Context\Initializer\OpenedEmailStorageContextInitializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class OpenedEmailStorageContextInitializerRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $openMailInitializer = new Definition(OpenedEmailStorageContextInitializer::class, [
            new Reference('mailpit.opened_email_storage'),
        ]);
        $openMailInitializer->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.opened_email_storage.context_initializer', $openMailInitializer);
    }
}
