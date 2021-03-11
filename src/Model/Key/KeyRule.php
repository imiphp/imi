<?php

namespace Imi\Model\Key;

class KeyRule
{
    /**
     * 规则.
     *
     * @var string
     */
    public $rule;

    /**
     * 参数名数组.
     *
     * @var string[]
     */
    public $paramNames;

    /**
     * @param string   $rule
     * @param string[] $paramNames
     */
    public function __construct($rule, $paramNames)
    {
        $this->rule = $rule;
        $this->paramNames = $paramNames;
    }
}
