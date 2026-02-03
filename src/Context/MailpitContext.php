<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Context;

use Exception;
use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use LibreSign\Mailpit\MailpitClient;
use LibreSign\Mailpit\Message\Contact;
use LibreSign\Mailpit\Specification\AndSpecification;
use LibreSign\Mailpit\Specification\AttachmentSpecification;
use LibreSign\Mailpit\Specification\BodySpecification;
use LibreSign\Mailpit\Specification\RecipientSpecification;
use LibreSign\Mailpit\Specification\SenderSpecification;
use LibreSign\Mailpit\Specification\Specification;
use LibreSign\Mailpit\Specification\SubjectSpecification;
use RuntimeException;

use function array_shift;
use function count;
use function sprintf;

/**
 * @psalm-suppress TooManyArguments
 */
final class MailpitContext implements MailpitAwareContext, OpenedEmailStorageAwareContext
{
    private MailpitClient $mailpitClient;
    private OpenedEmailStorage $openedEmailStorage;

    /**
     * @return Specification[]
     */
    private function getSpecifications(
        ?string $subject = null,
        ?string $body = null,
        ?string $from = null,
        ?string $recipient = null
    ): array {
        $specifications = [];

        if (!empty($subject)) {
            $specifications[] = new SubjectSpecification($subject);
        }

        if (!empty($body)) {
            $specifications[] = new BodySpecification($body);
        }

        if (!empty($from)) {
            $specifications[] = new SenderSpecification(Contact::fromString($from));
        }

        if (!empty($recipient)) {
            $specifications[] = new RecipientSpecification(Contact::fromString($recipient));
        }

        return $specifications;
    }

    #[\Override]
    public function setMailpit(MailpitClient $client): void
    {
        $this->mailpitClient = $client;
    }

    #[\Override]
    public function setOpenedEmailStorage(OpenedEmailStorage $storage): void
    {
        $this->openedEmailStorage = $storage;
    }

    /**
     * @Given /^my inbox is empty$/
     */
    public function myInboxIsEmpty(): void
    {
        $this->mailpitClient->purgeMessages();
    }

    /**
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)"$/
     * @Then /^I should see an email with body "(?P<body>[^"]*)"$/
     * @Then /^I should see an email from "(?P<from>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)" from "(?P<from>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" from "(?P<from>[^"]*)"$/
     * @Then /^I should see an email to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email with body "(?P<body>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email from "(?P<from>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)" from "(?P<from>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @Then /^I should see an email with subject "(?P<subject>[^"]*)" from "(?P<from>[^"]*)" to "(?P<recipient>[^"]*)"$/
     */
    public function iShouldSeeAnEmailWithSubjectAndBodyFromToRecipient(
        ?string $subject = null,
        ?string $body = null,
        ?string $from = null,
        ?string $recipient = null
    ): void {
        $specifications = $this->getSpecifications($subject, $body, $from, $recipient);

        $messages = $this->mailpitClient->findMessagesSatisfying(AndSpecification::all(...$specifications));

        if (count($messages) > 0) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'No message found%s%s%s%s',
                !empty($from) ? sprintf(' from "%s"', $from) : '',
                !empty($recipient) ? sprintf(' to "%s"', $recipient) : '',
                !empty($subject) ? sprintf(' with subject "%s"', $subject) : '',
                !empty($body) ? sprintf(' with body "%s"', $body) : ''
            )
        );
    }

    /**
     * @When /^I open the latest email from "(?P<from>[^"]*)"$/
     * @When /^I open the latest email to "(?P<recipient>[^"]*)"$/
     * @When /^I open the latest email with subject "(?P<subject>[^"]*)"$/
     * @When /^I open the latest email with body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email from "(?P<from>[^"]*)" to "(?P<recipient>[^"]*)"$/
     * @When /^I open the latest email from "(?P<from>[^"]*)" with subject "(?P<subject>[^"]*)"$/
     * @When /^I open the latest email to "(?P<recipient>[^"]*)" with subject "(?P<subject>[^"]*)"$/
     * @When /^I open the latest email from "(?P<from>[^"]*)" with body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email to "(?P<recipient>[^"]*)" with body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email from "(?P<from>[^"]*)" with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email to "(?P<recipient>[^"]*)" with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)"$/
     * @When /^I open the latest email from "(?P<from>[^"]*)" to "(?P<recipient>[^"]*)" with subject "(?P<subject>[^"]*)" and body "(?P<body>[^"]*)"$/
     */
    public function iOpenTheEmail(
        ?string $from = null,
        ?string $recipient = null,
        ?string $subject = null,
        ?string $body = null
    ): void {
        $specifications = $this->getSpecifications($subject, $body, $from, $recipient);

        $messages = $this->mailpitClient->findMessagesSatisfying(AndSpecification::all(...$specifications));

        if (count($messages) === 0) {
            throw new RuntimeException(
                sprintf(
                    'No message found%s%s%s',
                    !empty($from) ? sprintf(' from "%s"', $from) : '',
                    !empty($recipient) ? sprintf(' to "%s"', $recipient) : '',
                    !empty($subject) ? sprintf(' with subject "%s"', $subject) : ''
                )
            );
        }

        $this->openedEmailStorage->setOpenedEmail(array_shift($messages));
    }

    /**
     * @Then /^I should see "(?P<text>[^"]*)" in the opened email$/
     */
    public function iShouldSeeInTheOpenedEmail(string $text): void
    {
        if (!$this->openedEmailStorage->hasOpenedEmail()) {
            throw new RuntimeException('Unable to look for text in opened email - no email was opened yet');
        }

        $specification = new BodySpecification($text);

        if (!$specification->isSatisfiedBy($this->openedEmailStorage->getOpenedEmail())) {
            throw new Exception(sprintf('Could not find "%s" in email', $text));
        }
    }

    /**
     * @Then /^I should see an attachment with filename "(?P<filename>[^"]*)" in the opened email$/
     */
    public function iShouldAttachmentInOpenedEmail(string $filename): void
    {
        if (!$this->openedEmailStorage->hasOpenedEmail()) {
            throw new RuntimeException('Unable to look for text in opened email - no email was opened yet');
        }

        $specification = new AttachmentSpecification($filename);

        if (!$specification->isSatisfiedBy($this->openedEmailStorage->getOpenedEmail())) {
            throw new RuntimeException(
                sprintf('Opened email does not contain an attachment with filename "%s"', $filename)
            );
        }
    }

    /**
     * @Given /^I should see "([^"]*)" in email$/
     */
    public function iShouldSeeInEmail(string $text): void
    {
        $specification = new BodySpecification($text);

        $messages = $this->mailpitClient->findMessagesSatisfying($specification);

        if (count($messages) === 0) {
            throw new Exception(sprintf('Could not find "%s" in email', $text));
        }
    }

    /**
     * @Then /^there should be (\d+) email(?:s)? in my inbox$/
     */
    public function thereShouldBeEmailInMyInbox(int $numEmails): void
    {
        $numMailpitMessages = $this->mailpitClient->getNumberOfMessages();

        if ($numMailpitMessages !== $numEmails) {
            throw new Exception(
                sprintf(
                    'Expected %d messages in inbox, but there were %d',
                    $numEmails,
                    $numMailpitMessages
                )
            );
        }
    }

    /**
     * @Then /^I should see an email with attachment "([^"]*)"$/
     */
    public function iShouldSeeAnEmailWithAttachment(string $filename): void
    {
        $specification = new AttachmentSpecification($filename);

        $messages = $this->mailpitClient->findMessagesSatisfying($specification);

        if (count($messages) === 0) {
            throw new Exception(sprintf('Messages does not contain a message with attachment "%s"', $filename));
        }
    }
}
