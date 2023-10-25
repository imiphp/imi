<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inherit;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Table;

#[Inherit(annotation: 'Imi\\Bean\\Annotation\\Bean')]
#[Table]
class TestClass extends ParentClass
{
    /**
     * @var int
     */
    #[Inherit(annotation: ['Imi\\Model\\Annotation\\Column'])]
    public $id;

    /**
     * @var int
     */
    #[Inherit]
    public $id2;

    #[Inherit(annotation: '')]
    public const CCC = 1;

    #[Inherit]
    public const CCC2 = 1;

    #[Inherit(annotation: ['Imi\\Aop\\Annotation\\FilterArg'])]
    public function test(): void
    {
    }

    public function test2(): void
    {
    }

    #[Inherit]
    public function test3(): void
    {
    }
}
