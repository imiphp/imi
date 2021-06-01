<?php

declare(strict_types=1);

namespace Imi\JWT\Test\PHPUintListener;

use Imi\App;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Tool\Tool;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class ImiListener implements TestListener
{
    /**
     * @var bool
     */
    private $isLoadedImi = false;

    public function __construct()
    {
    }

    /**
     * An error occurred.
     */
    public function addError(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * A warning occurred.
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
    }

    /**
     * A failure occurred.
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
    }

    /**
     * Incomplete test.
     */
    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * Risky test.
     */
    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * Skipped test.
     */
    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
    }

    /**
     * A test suite started.
     */
    public function startTestSuite(TestSuite $suite): void
    {
    }

    /**
     * A test suite ended.
     */
    public function endTestSuite(TestSuite $suite): void
    {
    }

    /**
     * A test started.
     */
    public function startTest(Test $test): void
    {
        if (!$this->isLoadedImi)
        {
            Event::on('IMI.INIT_TOOL', function (EventParam $param) {
                $data = $param->getData();
                $data['skip'] = true;
                Tool::init();
                $this->isLoadedImi = true;
            });
            Event::on('IMI.INITED', function (EventParam $param) {
                App::initWorker();
                go(function () use ($param) {
                    $param->stopPropagation();
                });
            }, 1);
            echo 'init imi...', \PHP_EOL;
            App::run('Imi\JWT\Test');
            echo 'imi inited!', \PHP_EOL;
        }
    }

    /**
     * A test ended.
     */
    public function endTest(Test $test, float $time): void
    {
    }
}
