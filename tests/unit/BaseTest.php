<?php
namespace Imi\Test;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function go($callable, $finally = null)
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
        $cmd = PHP_BINARY . " {$phpFile} {$args}";
        return `{$cmd}`;
    }

}