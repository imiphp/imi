<?php

declare(strict_types=1);

namespace Imi\Test\Component\Inherit;

use Imi\Aop\Annotation\FilterArg;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Annotation\Callback;
use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Test\Component\Annotation\CommentAnnotation;

#[Entity]
#[Bean]
class ParentClass
{
    #[Column]
    #[Callback(class: 'Test', method: 'test')]
    public int $id;

    #[Column]
    #[Callback(class: 'Test', method: 'test')]
    public int $id2;

    #[Inherit]
    public const CCC = 1;

    #[Inherit]
    public const CCC2 = 1;

    #[FilterArg]
    #[CommentAnnotation]
    public function test(): void
    {
    }

    #[FilterArg]
    #[CommentAnnotation]
    public function test2(): void
    {
    }

    #[FilterArg]
    #[CommentAnnotation]
    public function test3(): void
    {
    }
}
