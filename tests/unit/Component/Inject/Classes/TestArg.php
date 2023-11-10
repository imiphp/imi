<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inject\Classes;

use Imi\Aop\Annotation\FilterArg;
use Imi\Aop\Annotation\InjectArg;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;

#[Bean(name: 'TestArg')]
class TestArg
{
    #[FilterArg(name: 'id', filter: 'intval')]
    #[InjectArg(name: 'phpVersion', value: \PHP_VERSION)]
    public function test(int $id, ?string $phpVersion = null): void
    {
        Assert::assertTrue(\is_int($id));
        Assert::assertEquals(\PHP_VERSION, $phpVersion);
    }
}
