<?php

declare(strict_types=1);

namespace Imi\Test\Component\Annotation;

use Imi\Aop\Annotation\Inject;
use Imi\Bean\Annotation\Bean;
use Imi\Enum\Annotation\EnumItem;
use Imi\Server\Http\Route\Annotation\Route;

#[Bean(name: 'TestAnnotationPHP8')]
class TestAnnotationPHP8
{
    #[EnumItem(text: 'test')]
    public const CONST_VALUE = 1;

    /**
     * @var \Imi\Log\ErrorLog
     */
    #[Inject(name: 'ErrorLog')]
    protected $errorLog;

    #[Route(url: '/testAnnotation')]
    public function test(): void
    {
    }
}
