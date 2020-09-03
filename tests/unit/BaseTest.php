<?php
namespace Imi\Test;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

if(class_exists(TestCase::class))
{
    abstract class BaseTest extends TestCase
    {
        const PERFORMANCE_COUNT = 10000;
    
        protected function go($callable, $finally = null)
        {
            $throwable = null;
            $cid = imigo(function() use($callable, &$throwable){
                try {
                    $callable();
                } catch(\Throwable $th) {
                    $throwable = $th;
                }
            });
            while(Coroutine::exists($cid))
            {
                usleep(10000);
            }
            if($finally)
            {
                $finally();
            }
            if($throwable)
            {
                throw $throwable;
            }
        }
    
        protected function php($phpFile, $args = '')
        {
            $cmd = \Imi\cmd('"' . PHP_BINARY . "\" \"{$phpFile}\" {$args}");
            return `{$cmd}`;
        }
    
        public function startTest()
        {
            static $run = false;
            if(!$run)
            {
                $run = true;
                $this->__startTest();
            }
        }
    
        public function __startTest()
        {
    
        }
    
    }
}
else
{
    // 当 PHPUnit 未引入时，直接执行命令时的支持
    abstract class BaseTest
    {

    }
}
