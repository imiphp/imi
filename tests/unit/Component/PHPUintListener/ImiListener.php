<?php
namespace Imi\Test\Component\PHPUintListener;

use Imi\App;
use Imi\Tool\Tool;
use Imi\Event\Event;
use Imi\Event\EventParam;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\Warning;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\AssertionFailedError;
use Imi\Pool\PoolManager;
use Imi\Db\Interfaces\IDb;

class ImiListener implements TestListener
{
    private $isLoadedImi = false;

    /**
     * @var \PHPUnit\Framework\TestSuite
     */
    private $suite;

    /**
     *
     * @var \PHPUnit\Util\TestDox\NamePrettifier
     */
    private $namePrettifier;

    private $success;

    private $errorMessage;

    private $messageColor;

    private $isOb = false;
    
    const COLOR_RED = 1;

    const COLOR_GREEN = 2;

    const COLOR_YELLOW = 3;

    public function __construct()
    {
        $this->namePrettifier = new \PHPUnit\Util\TestDox\NamePrettifier;
    }

    /**
     * An error occurred.
     */
    public function addError(Test $test, \Throwable $t, float $time): void
    {
        $this->errorMessage = 'Error';
        $this->messageColor = static::COLOR_RED;
        $this->success = false;
        $this->startOb();
    }

    /**
     * A warning occurred.
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
        $this->errorMessage = 'Warning';
        $this->messageColor = static::COLOR_YELLOW;
        $this->success = false;
        $this->startOb();
    }

    /**
     * A failure occurred.
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        $this->errorMessage = 'Failure';
        $this->messageColor = static::COLOR_RED;
        $this->success = false;
        $this->startOb();
    }

    /**
     * Incomplete test.
     */
    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        $this->errorMessage = 'IncompleteTest';
        $this->messageColor = static::COLOR_YELLOW;
        $this->success = false;
        $this->startOb();
    }

    /**
     * Risky test.
     */
    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        $this->errorMessage = 'RiskyTest';
        $this->messageColor = static::COLOR_YELLOW;
        $this->success = false;
        $this->startOb();
    }

    /**
     * Skipped test.
     */
    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        $this->errorMessage = 'SkippedTest';
        $this->messageColor = null;
        $this->success = false;
        $this->startOb();
    }

    /**
     * A test suite started.
     */
    public function startTestSuite(TestSuite $suite): void
    {
        $this->suite = $suite;
    }

    /**
     * A test suite ended.
     */
    public function endTestSuite(TestSuite $suite): void
    {
        $this->stopOb(1);
    }

    /**
     * A test started.
     */
    public function startTest(Test $test): void
    {
        if(!$this->isLoadedImi)
        {
            Event::on('IMI.INIT_TOOL', function(EventParam $param){
                $data = $param->getData();
                $data['skip'] = true;
                Tool::init();
                $this->isLoadedImi = true;
            });
            Event::on('IMI.INITED', function(EventParam $param){
                App::initWorker();
                go(function() use($param){
                    $param->stopPropagation();
                    PoolManager::use('maindb', function($resource, IDb $db){
                        $truncateList = [
                            'tb_article',
                            'tb_member',
                            'tb_update_time',
                        ];
                        foreach($truncateList as $table)
                        {
                            $db->exec('TRUNCATE ' . $table);
                        }
                    });
                });
            }, 1);
            echo 'init imi...', PHP_EOL;
            App::run('Imi\Test\Component');
            echo 'imi inited!', PHP_EOL;
        }
        if(method_exists($test, '__autoInject'))
        {
            $methodRef = new \ReflectionMethod($test, '__autoInject');
            $methodRef->setAccessible(true);
            $methodRef->invoke($test);
        }
        $this->stopOb(1);
        echo PHP_EOL, 'TEST ', $this->namePrettifier->prettifyTestClass($this->suite->getName()), ' ', $this->namePrettifier->prettifyTestCase($test);
        $this->success = true;
        $this->startOb();
    }

    /**
     * A test ended.
     */
    public function endTest(Test $test, float $time): void
    {
        $this->stopOb();
        echo ' ', round(($time * 1000), 3) . 'ms ';
        if($this->success)
        {
            $this->write('√', static::COLOR_GREEN);
        }
        else
        {
            $this->write($this->errorMessage, $this->messageColor);
        }
        $this->startOb();
    }

    private function write($message, $color = null)
    {
        switch($color)
        {
            case static::COLOR_RED:
                echo "\033[31m {$message} \033[0m";
                break;
            case static::COLOR_GREEN:
                echo "\033[32m {$message} \033[0m";
                break;
            case static::COLOR_YELLOW:
                echo "\033[33m {$message} \033[0m";
                break;
            default:
                echo $message;
        }
    }

    private function startOb()
    {
        if(!$this->isOb)
        {
            ob_start();
            $this->isOb = true;
        }
    }

    private function stopOb($skipLength = null)
    {
        if($this->isOb)
        {
            if($skipLength)
            {
                $content = ob_get_clean();
                echo substr($content, $skipLength);
            }
            else
            {
                ob_end_clean();
            }
            $this->isOb = false;
        }
    }
}