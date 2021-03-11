<?php

namespace Imi\Aop;

class AroundJoinPoint extends JoinPoint
{
    /**
     * process调用的方法.
     *
     * @var callable
     */
    protected $nextProceed;

    /**
     * @param string              $type
     * @param string              $method
     * @param array               $args
     * @param object              $target
     * @param \Imi\Bean\BeanProxy $_this
     * @param callable            $nextProceed
     */
    public function __construct($type, $method, &$args, $target, $_this, $nextProceed)
    {
        parent::__construct($type, $method, $args, $target, $_this);
        $this->nextProceed = $nextProceed;
    }

    /**
     * 调用下一个方法.
     *
     * @param array|null $args
     *
     * @return mixed
     */
    public function proceed($args = null)
    {
        if (null === $args)
        {
            $args = $this->getArgs();
        }
        $result = ($this->nextProceed)($args);

        $this->args = $args;

        return $result;
    }
}
