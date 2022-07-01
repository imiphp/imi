<?php

declare(strict_types=1);

namespace Imi\Test\Component;

use Imi\App;
use Imi\Cli\CliApp;
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
                    'tb_article2',
                    'tb_article_ex',
                    'tb_member',
                    'tb_member_role_relation',
                    'tb_update_time',
                    'tb_performance',
                    'tb_polymorphic',
                    'tb_test_json',
                    'tb_test_list',
                    'tb_test_soft_delete',
                    'tb_test_enum',
                    'tb_test_set',
                    'tb_test_with_member',
                    'tb_virtual_column',
                ];
                foreach ($truncateList as $table)
                {
                    $db->exec('TRUNCATE ' . $table);
                }
            });
        }, 1);
        try
        {
            App::run('Imi\Test\Component', CliApp::class, static function () {
            });
        }
        catch (\Throwable $exception)
        {
            var_dump((string) $exception); // 方便错误调试查看
            throw $exception;
        }
    }
}
