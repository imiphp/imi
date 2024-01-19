<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

use Imi\App;
use Imi\Core\CoreEvents;
use Imi\Db\Db;
use Imi\Db\Interfaces\IDb;
use Imi\Event\Contract\IEvent;
use Imi\Event\Event;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class PHPUnitHook implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        Event::on(CoreEvents::APP_RUN, static function (IEvent $param): void {
            $param->stopPropagation();
            Db::use(static function (IDb $db): void {
                foreach ([
                    'tb_article',
                    'tb_member',
                    'tb_update_time',
                    'tb_performance',
                    'tb_no_inc_pk',
                ] as $table)
                {
                    $db->exec('TRUNCATE ' . $table . ' RESTART IDENTITY');
                }
            }, \in_array('pgsql', pdo_drivers()) ? 'maindb' : 'swoole');
        }, 1);
        App::run('Imi\Pgsql\Test', SwooleApp::class, static function (): void {
        });
    }
}
