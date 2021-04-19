<?php

namespace Imi\Test\Component\Util\ClassObject;

class Test
{
    /**
     * @var mixed
     */
    public $a;

    /**
     * @var mixed
     */
    public $b;

    /**
     * @var string
     */
    public $c;

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $c
     */
    public function __construct($a, $b, $c = 'imi.com')
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $c
     *
     * @return void
     */
    public function imi($a, $b, $c = 'imi.com')
    {
    }
}
