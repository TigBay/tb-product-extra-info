<?php

// Source - https://stackoverflow.com/a/68637860
// Posted by Alister Bulman
// Retrieved 2026-09-18, License - CC BY-SA 4.0

namespace Tb\Tests\Unit;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\Before;

/**
 * Create a dummy logger that can be inspected.
 *
 * Use `$this->log` on a LoggerInterface parameter to send logs to.
 * Make assertions on the contents of `$this->testLog`.
 *
 * Examples:
 * <code>
 *  * $this->testLog->reset();      // clear the previously log messages
 *  * $this->assertCount(1, $this->testLog->getRecords(), 'Only expected 1 log     message');
 *  * $this->assertTrue($this->testLog->hasDebugThatMatches('/from the API/'));
 * </code>
 */
trait TestLog
{
    protected Logger $log;
    protected TestHandler $testLog;

    #[Before]
    protected function createTestLog(): void
    {
        $this->log = new Logger('test');
        $this->testLog = new TestHandler();
        $this->log->pushHandler($this->testLog);
    }
}
