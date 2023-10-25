<?php

declare(strict_types=1);

namespace Imi\Test\Component\RequestContextProxy;

use Imi\RequestContextProxy\Annotation\RequestContextProxy;
use Imi\RequestContextProxy\BaseRequestContextProxy;

/**
 * @method        mixed add($a, $b)
 * @method static mixed add($a, $b)
 */
#[RequestContextProxy(class: \Imi\Test\Component\RequestContextProxy\A::class, name: 'testRequestContextProxyA')]
class RequestContextProxyA extends BaseRequestContextProxy
{
}
