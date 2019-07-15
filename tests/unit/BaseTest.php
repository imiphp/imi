<?php
namespace Imi\Test;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function go($callable)
    {
        $throwable = null;
        $end = false;
        imigo(function() use($callable, &$throwable, &$end){
            try {
                $callable();
            } catch(\Throwable $th) {
                $throwable = $th;
            }
            $end = true;
        });
        while(!$end)
        {
            \Swoole\Event::dispatch();
        }
        if($throwable)
        {
            throw $throwable;
        }
    }

}