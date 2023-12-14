<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator\Contract;

use Imi\Model\BaseModel;

/**
 * ID 生成器接口.
 */
interface IIdGenerator
{
    public function generate(?BaseModel $model, array $options = []): mixed;
}
