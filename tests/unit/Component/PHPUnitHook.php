<?php

declare(strict_types=1);

namespace Imi\Test\Component;

use Imi\App;
use Imi\Cli\CliApp;
use Imi\Db\Interfaces\IDb;
use Imi\Event\Contract\IEvent;
use Imi\Event\Event;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class PHPUnitHook implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        Event::on('IMI.APP_RUN', static function (IEvent $param): void {
            $param->stopPropagation();
            if (file_exists($file = __DIR__ . '/.runtime/test'))
            {
                shell_exec("rm -rf {$file}");
            }
            PoolManager::use('maindb', static function (IPoolResource $resource, IDb $db): void {
                foreach ([
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
                    'tb_test_field_name',
                    'tb_no_inc_pk',
                ] as $table)
                {
                    $db->exec('TRUNCATE ' . $table);
                }
            });
        }, 1);
        try
        {
            App::run('Imi\Test\Component', CliApp::class, static function (): void {
            });
        }
        catch (\Throwable $th)
        {
            var_dump((string) $th); // 方便错误调试查看
            throw $th;
        }
    }
}
