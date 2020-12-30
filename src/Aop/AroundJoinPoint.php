<?php

declare(strict_types=1);

namespace Imi\Aop;

class AroundJoinPoint extends JoinPoint
{
    /**
     * process调用的方法.
     *
     * @var callable
     */
    private $nextProceed;

    public function __construct(string $type, string $method, array &$args, object $target, callable $nextProceed)
    {
        parent::__construct($type, $method, $args, $target);
        $this->nextProceed = $nextProceed;
    }

    /**
     * 调用下一个方法.
     *
     * @param array|null $args
     *
     * @return mixed
     */
    public function proceed(?array $args = null)
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
