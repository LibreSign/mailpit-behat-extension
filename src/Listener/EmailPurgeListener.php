<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function array_merge;

final class EmailPurgeListener implements EventSubscriberInterface
{
    private MailpitClient $client;

    /**
     * The tag name for scenarios/features that trigger
     * a mailpit purge. Defaults to 'email'.
     *
     * @var string
     */
    private $purgeTag;

    public function __construct(MailpitClient $client, string $purgeTag)
    {
        $this->client = $client;
        $this->purgeTag = $purgeTag;
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            ScenarioTested::BEFORE => ['purgeEmails', 10],
            ExampleTested::BEFORE => ['purgeEmails', 10],
        ];
    }

    public function purgeEmails(ScenarioLikeTested $event): void
    {
        $scenario = $event->getScenario();
        $feature  = $event->getFeature();

        foreach (array_merge($feature->getTags(), $scenario->getTags()) as $tag) {
            if ($this->purgeTag === $tag) {
                $this->client->purgeMessages();

                return;
            }
        }
    }
}
