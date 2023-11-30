<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

use Imi\App;
use Imi\Db\Interfaces\IDb;
use Imi\Event\Contract\IEvent;
use Imi\Event\Event;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use Imi\Swoole\SwooleApp;
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
            PoolManager::use(\in_array('pgsql', pdo_drivers()) ? 'maindb' : 'swoole', static function (IPoolResource $resource, IDb $db): void {
                $truncateList = [
                    'tb_article',
                    'tb_member',
                    'tb_update_time',
                    'tb_performance',
                    'tb_no_inc_pk',
                ];
                foreach ($truncateList as $table)
                {
                    $db->exec('TRUNCATE ' . $table . ' RESTART IDENTITY');
                }
            });
        }, 1);
        App::run('Imi\Pgsql\Test', SwooleApp::class, static function (): void {
        });
    }
}
