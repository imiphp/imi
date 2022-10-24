<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

if (\PHP_VERSION_ID >= 80200)
{
    eval(<<<'PHP'
    if (!class_exists(ReadOnlyBean::class, false))
    {
        /**
         * @Bean("ReadOnlyBean")
         */
        readonly class ReadOnlyBean
        {
        }
    }
    PHP);
}
