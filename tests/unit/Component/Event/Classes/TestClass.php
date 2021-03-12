<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Classes;

use Imi\Event\TEvent;

class TestClass
{
    use TEvent;

    /**
     * @return mixed
     */
    public function test1()
    {
        $return = null;
        $this->trigger('test1', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);

        return $return;
    }

    /**
     * @return mixed
     */
    public function test2()
    {
        $return = null;
        $this->trigger('test2', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);

        return $return;
    }

    /**
     * @return mixed
     */
    public function test3()
    {
        $return = null;
        $this->trigger('test3', [
            'name'   => 'imi',
            'return' => &$return,
        ], $this);

        return $return;
    }
}
