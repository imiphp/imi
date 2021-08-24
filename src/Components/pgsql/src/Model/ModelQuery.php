<?php

declare(strict_types=1);

namespace Imi\Pgsql\Model;

use Imi\Model\Traits\TModelQuery;
use Imi\Pgsql\Db\Query\PgsqlQuery;

class ModelQuery extends PgsqlQuery
{
    use TModelQuery;
}
