<?php

namespace Imi\Server\MQTT\Exception;

/**
 * 非法的 MQTT 协议数据.
 */
class InvalidReceiveData extends \InvalidArgumentException
{
    public function __construct(string $message = 'Invalid MQTT receive data', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
