<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class EmailPurgeListenerRegistrar
{
    public function register(ContainerBuilder $container): void
    {
        $listener = new Definition(EmailPurgeListener::class, [
            new Reference('mailpit.client'),
            '%mailpit.purge_tag%',
        ]);
        $listener->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.purge_listener', $listener);
    }
}
