<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 自动插入.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property bool $status 是否开启
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoInsert extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'status';

    public function __construct(?array $__data = null, bool $status = true)
    {
        parent::__construct(...\func_get_args());
    }
}
