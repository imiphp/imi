<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

use Imi\App;
use Imi\Db\Interfaces\IDb;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use PHPUnit\Runner\BeforeFirstTestHook;

class PHPUnitHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        Event::on('IMI.APP_RUN', function (EventParam $param) {
            $param->stopPropagation();
            PoolManager::use('maindb', function (IPoolResource $resource, IDb $db) {
                $truncateList = [
                    'tb_article',
                    'tb_member',
                    'tb_update_time',
                    'tb_performance',
                ];
                foreach ($truncateList as $table)
                {
                    $db->exec('TRUNCATE ' . $table . ' RESTART IDENTITY');
                }
            });
        }, 1);
        App::run('Imi\Pgsql\Test', TestApp::class);
    }
}
