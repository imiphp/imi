<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Classes;

use Imi\Event\TEvent;

class TestClass
{
    use TEvent;

    public function test1(): mixed
    {
        $event = new TestClassEvent('test1', $this, 'imi', null);
        $this->dispatch($event);

        return $event->return;
    }

    public function test2(): mixed
    {
        $event = new TestClassEvent('test2', $this, 'imi', null);
        $this->dispatch($event);

        return $event->return;
    }

    public function test3(): mixed
    {
        $event = new TestClassEvent('test3', $this, 'imi', null);
        $this->dispatch($event);

        return $event->return;
    }
}
