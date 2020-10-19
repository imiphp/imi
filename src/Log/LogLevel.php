<?php

namespace Imi\Log;

class LogLevel extends \Psr\Log\LogLevel
{
    const ALL = [
        self::ALERT,
        self::CRITICAL,
        self::DEBUG,
        self::EMERGENCY,
        self::ERROR,
        self::INFO,
        self::NOTICE,
        self::WARNING,
    ];
}
