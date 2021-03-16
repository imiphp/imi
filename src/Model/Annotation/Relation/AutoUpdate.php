<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 自动更新.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class AutoUpdate extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'status';

    /**
     * 是否开启.
     */
    public bool $status = true;

    /**
     * save时，删除无关联数据.
     */
    public bool $orphanRemoval = false;

    public function __construct(?array $__data = null, bool $status = true, bool $orphanRemoval = false)
    {
        parent::__construct(...\func_get_args());
    }
}
