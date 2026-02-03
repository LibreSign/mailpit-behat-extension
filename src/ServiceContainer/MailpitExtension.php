<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use LibreSign\Behat\MailpitExtension\Context\Initializer\MailpitAwareInitializer;
use LibreSign\Behat\MailpitExtension\Context\Initializer\OpenedEmailStorageContextInitializer;
use LibreSign\Behat\MailpitExtension\Listener\EmailPurgeListener;
use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpClient\Psr18Client;

/**
 * This class configures a lot of services, so needs access
 * to a lot of classes. Therefore high coupling is allowed here.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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

        $this->registerHttpClient($container);
        $this->registerMailpitClient($container);
        $this->registerContextInitializer($container);
        $this->registerPurgeListener($container);

        $this->registerOpenedEmailStorage($container);
        $this->registerOpenedEmailStorageContextInitializer($container);
    }

    private function registerHttpClient(ContainerBuilder $container): void
    {
        $httpClient = new Definition(Psr18Client::class);

        $container->setDefinition('mailpit.http_client', $httpClient);
        $container->setDefinition('mailpit.http_request_factory', $httpClient);
        $container->setDefinition('mailpit.http_stream_factory', $httpClient);
    }

    private function registerMailpitClient(ContainerBuilder $container): void
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

    private function registerContextInitializer(ContainerBuilder $container): void
    {
        $contextInitializer = new Definition(MailpitAwareInitializer::class, [
            new Reference('mailpit.client'),
        ]);

        $contextInitializer->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.context_initializer', $contextInitializer);
    }

    private function registerOpenedEmailStorage(ContainerBuilder $container): void
    {
        $openedEmailStorage = new Definition(OpenedEmailStorage::class);

        $container->setDefinition('mailpit.opened_email_storage', $openedEmailStorage);
    }

    private function registerOpenedEmailStorageContextInitializer(ContainerBuilder $container): void
    {
        $openMailInitializer = new Definition(OpenedEmailStorageContextInitializer::class, [
            new Reference('mailpit.opened_email_storage'),
        ]);
        $openMailInitializer->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.opened_email_storage.context_initializer', $openMailInitializer);
    }

    private function registerPurgeListener(ContainerBuilder $container): void
    {
        $listener = new Definition(EmailPurgeListener::class, [
            new Reference('mailpit.client'),
            '%mailpit.purge_tag%',
        ]);
        $listener->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        $container->setDefinition('mailpit.purge_listener', $listener);
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
