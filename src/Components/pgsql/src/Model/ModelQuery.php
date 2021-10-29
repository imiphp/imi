<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model;

use Imi\Model\Contract\IModelQuery;
use Imi\Model\Traits\TModelQuery;
use Imi\Pgsql\Db\Query\PgsqlQuery;

class ModelQuery extends PgsqlQuery implements IModelQuery
{
    use TModelQuery;
}
