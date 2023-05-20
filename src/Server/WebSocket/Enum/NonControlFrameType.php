<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Enum;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * Websocket 非控制帧类型.
 */
class NonControlFrameType extends BaseEnum
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * @EnumItem("文本帧")
     */
    public const TEXT = 1;

    /**
     * @EnumItem("二进制帧")
     */
    public const BINARY = 2;
}
