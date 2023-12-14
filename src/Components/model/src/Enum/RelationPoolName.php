<?php

declare(strict_types=1);

namespace Imi\Model\Enum;

/**
 * 模型关联的连接池名.
 */
class RelationPoolName
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 父模型连接池.
     */
    public const PARENT = 1;

    /**
     * 被关联的模型本身的连接池配置.
     */
    public const RELATION = 2;
}
