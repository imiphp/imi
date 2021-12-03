<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 自动更新.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property bool $status        是否开启
 * @property bool $orphanRemoval save时，删除无关联数据
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoUpdate extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'status';

    public function __construct(?array $__data = null, bool $status = true, bool $orphanRemoval = false)
    {
        parent::__construct(...\func_get_args());
    }
}
