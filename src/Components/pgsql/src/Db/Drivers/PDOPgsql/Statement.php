<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\PDOPgsql;

use Imi\Db\Drivers\PDO\TPDOStatement;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;
use Imi\Util\Text;

/**
 * PDO Pgsql驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    use TPDOStatement;

    /**
     * 更新最后插入ID.
     */
    protected function updateLastInsertId(): void
    {
        $queryString = $this->statement->queryString;
        if (Text::startwith($queryString, 'insert ', false) || Text::startwith($queryString, 'replace ', false))
        {
            try
            {
                $this->lastInsertId = $this->db->lastInsertId();
            }
            catch (\Throwable $th)
            {
                if (!str_contains($th->getMessage(), 'lastval is not yet defined in this session'))
                {
                    throw $th;
                }
            }
        }
        else
        {
            $this->lastInsertId = '';
        }
    }
}
