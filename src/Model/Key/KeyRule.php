<?php

declare(strict_types=1);

namespace Imi\Model\Key;

class KeyRule
{
    /**
     * 规则.
     */
    public string $rule = '';

    /**
     * 参数名数组.
     *
     * @var string[]
     */
    public array $paramNames = [];

    public function __construct(string $rule, array $paramNames)
    {
        $this->rule = $rule;
        $this->paramNames = $paramNames;
    }
}
