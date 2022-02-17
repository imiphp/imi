<?php

declare(strict_types=1);

namespace Imi\RateLimit\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 阻塞等待消费.
 *
 * 当触发限流时，自动阻塞（协程挂起）等待
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @property int|null $timeout 超时时间，单位：秒；为 null 不限制
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class BlockingConsumer extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'timeout';

    public function __construct(?array $__data = null, ?int $timeout = null)
    {
        parent::__construct(...\func_get_args());
    }
}
