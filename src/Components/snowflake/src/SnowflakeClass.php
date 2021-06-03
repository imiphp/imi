<?php

declare(strict_types=1);

namespace Imi\Snowflake;

use Godruoyi\Snowflake\Snowflake;
use Imi\Worker;

class SnowflakeClass extends Snowflake
{
    public function __construct(int $datacenter = null, int $workerid = null)
    {
        if (null === $workerid)
        {
            $workerid = Worker::getWorkerId();
        }
        parent::__construct($datacenter, $workerid);
    }

    public function getWorkerId(): int
    {
        return $this->workerid;
    }

    public function getDatacenter(): int
    {
        return $this->datacenter;
    }
}
