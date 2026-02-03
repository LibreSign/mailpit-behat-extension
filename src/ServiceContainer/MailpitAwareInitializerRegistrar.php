<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class MailpitAwareInitializerRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $contextInitializer = new Definition(MailpitAwareInitializer::class, [
            new Reference('mailpit.client'),
        ]);

        $contextInitializer->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.context_initializer', $contextInitializer);
    }
}
