<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Gherkin\Node\TaggedNodeInterface;
use LibreSign\Mailpit\MailpitClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WeakMap;

use function array_merge;

final class EmailPurgeListener implements EventSubscriberInterface
{
    private MailpitClient $client;

    /** @var WeakMap<ScenarioLikeInterface, bool> */
    private WeakMap $purgedScenarios;

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
        $this->purgedScenarios = new WeakMap();
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
        $this->purgeFor($event->getFeature(), $event->getScenario());
    }

    public function purgeFor(FeatureNode $feature, ScenarioLikeInterface $scenario): void
    {
        if (isset($this->purgedScenarios[$scenario])) {
            return;
        }

        $scenarioTags = $scenario instanceof TaggedNodeInterface ? $scenario->getTags() : [];

        foreach (array_merge($feature->getTags(), $scenarioTags) as $tag) {
            if ($this->purgeTag === $tag) {
                $this->client->purgeMessages();
                $this->purgedScenarios[$scenario] = true;

                return;
            }
        }
    }
}
