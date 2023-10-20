<?php

declare(strict_types=1);

namespace Imi\Model\Key;

class KeyRule
{
    public function __construct(
        /**
         * 规则.
         */
        public string $rule,
        /**
         * 参数名数组.
         *
         * @var string[]
         */
        public array $paramNames
    ) {
    }
}
