<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Db\Mysql\Query\MysqlQuery;
use Imi\Model\Contract\IModelQuery;
use Imi\Model\Traits\TModelQuery;

/**
 * 模型查询器.
 */
class ModelQuery extends MysqlQuery implements IModelQuery
{
    use TModelQuery;
}
