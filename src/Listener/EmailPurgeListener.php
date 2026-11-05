<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\TaggedNodeInterface;
use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function array_merge;
use function ltrim;

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

    public function purgeEmails(ScenarioTested $event): void
    {
        $scenario = $event->getScenario();
        $feature  = $event->getFeature();

        $scenarioTags = $scenario instanceof TaggedNodeInterface ? $scenario->getTags() : [];

        foreach (array_merge($feature->getTags(), $scenarioTags) as $tag) {
            if ($this->purgeTag === ltrim($tag, '@')) {
                $this->client->purgeMessages();

                return;
            }
        }
    }
}
