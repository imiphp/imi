<?php

declare(strict_types=1);

namespace Imi\Snowflake\Model;

use Imi\Model\BaseModel;
use Imi\Model\IdGenerator\Contract\IIdGenerator;
use Imi\Snowflake\SnowflakeUtil;

class SnowflakeGenerator implements IIdGenerator
{
    public function generate(?BaseModel $model, array $options = []): mixed
    {
        if (!isset($options['name']))
        {
            throw new \InvalidArgumentException('The name option in the snowflake is required.');
        }

        return SnowflakeUtil::id($options['name']);
    }
}
