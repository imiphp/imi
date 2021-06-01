<?php

declare(strict_types=1);

namespace Imi\AMQP\Queue;

use Imi\AMQP\Message;
use Imi\Util\Format\Json;

class JsonAMQPMessage extends Message
{
    /**
     * 格式处理.
     *
     * @var string
     */
    protected $format = Json::class;
}
