<?php

declare(strict_types=1);

namespace Imi\Test\Component\Bean;

// Bean
use Imi\Bean\Annotation\Bean;

if (\PHP_VERSION_ID >= 80200)
{
    eval(<<<'PHP'
    namespace Imi\Test\Component\Bean;

    use Imi\Bean\Annotation\Bean;

    if (!class_exists(ReadOnlyBean::class, false))
    {
        #[Bean('ReadOnlyBean')]
        readonly class ReadOnlyBean
        {
            public function test(): string
            {
                return 'ReadOnlyBean';
            }
        }
    }
    PHP);
}
