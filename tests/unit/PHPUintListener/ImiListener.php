<?php
namespace Imi\Test\PHPUintListener;

use Imi\App;
use Imi\Tool\Tool;
use Imi\Event\Event;
use Imi\Event\EventParam;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\Warning;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\AssertionFailedError;

class ImiListener implements TestListener
{
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
        if(!$this->isLoadedImi)
        {
            Event::on('IMI.INITED', function(EventParam $param){
                $param->stopPropagation();
                Tool::init();
                $this->isLoadedImi = true;
                echo 'imi inited!', PHP_EOL;
            }, PHP_INT_MAX);
            echo 'init imi...', PHP_EOL;
            App::run('Imi\Test');
        }
        if(method_exists($test, '__autoInject'))
        {
            $methodRef = new \ReflectionMethod($test, '__autoInject');
            $methodRef->setAccessible(true);
            $methodRef->invoke($test);
        }
    }

    /**
     * A test ended.
     */
    public function endTest(Test $test, float $time): void
    {
        
    }

}