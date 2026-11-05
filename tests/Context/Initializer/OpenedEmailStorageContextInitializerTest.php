<?php
declare(strict_types=1);

namespace LibreSign\Behat\MailpitExtension\Tests\Context\Initializer;

use Behat\Behat\Context\Context;
use LibreSign\Behat\MailpitExtension\Context\Initializer\OpenedEmailStorageContextInitializer;
use LibreSign\Behat\MailpitExtension\Context\OpenedEmailStorageAwareContext;
use LibreSign\Behat\MailpitExtension\Service\OpenedEmailStorage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OpenedEmailStorageContextInitializerTest extends TestCase
{
    #[Test]
    public function it_injects_storage_into_an_aware_context(): void
    {
        $context = new class implements OpenedEmailStorageAwareContext {
            public ?OpenedEmailStorage $storage = null;

            public function setOpenedEmailStorage(OpenedEmailStorage $storage): void
            {
                $this->storage = $storage;
            }
        };
        $storage = new OpenedEmailStorage();

        (new OpenedEmailStorageContextInitializer($storage))->initializeContext($context);

        $this->assertSame($storage, $context->storage);
    }

    #[Test]
    public function it_ignores_contexts_that_do_not_support_storage(): void
    {
        $context = new class implements Context {
        };

        (new OpenedEmailStorageContextInitializer(new OpenedEmailStorage()))->initializeContext($context);

        $this->assertInstanceOf(Context::class, $context);
    }
}
