<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Enum;

/**
 * Websocket 非控制帧类型.
 */
enum NonControlFrameType
{
    /**
     * 文本帧.
     */
    case Text;

    /**
     * 二进制帧.
     */
    case Binary;
}
