<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MailpitExtension implements Extension
{
    #[\Override]
    public function getConfigKey(): string
    {
        return 'mailpit';
    }

    #[\Override]
    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('base_url')->isRequired()->end()
                ->scalarNode('purge_tag')->defaultValue('email')->end()
            ->end();
    }

    /**
     * @param array<string, mixed> $config
     */
    #[\Override]
    public function load(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('mailpit.base_url', $config['base_url']);
        $container->setParameter('mailpit.purge_tag', $config['purge_tag']);

        (new HttpClientRegistrar())->register($container);
        (new MailpitClientRegistrar())->register($container);
        (new MailpitAwareInitializerRegistrar())->register($container);
        (new OpenedEmailStorageRegistrar())->register($container);
        (new OpenedEmailStorageContextInitializerRegistrar())->register($container);
        (new EmailPurgeListenerRegistrar())->register($container);
    }

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
    }

    #[\Override]
    public function initialize(ExtensionManager $extensionManager): void
    {
    }
}
