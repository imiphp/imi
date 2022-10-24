<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

if (\PHP_VERSION_ID >= 80200)
{
    eval(<<<'PHP'
    /**
     * @Bean("ReadOnlyBean")
     */
    readonly class ReadOnlyBean
    {
    }
    PHP);
}
