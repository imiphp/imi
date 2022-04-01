<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component;

use Imi\App;
use Imi\Db\Interfaces\IDb;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use Imi\Swoole\SwooleApp;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class PHPUnitHook implements BeforeFirstTestHook, AfterLastTestHook
{
    private Channel $channel;

    public function executeBeforeFirstTest(): void
    {
        $this->channel = $channel = new Channel(1);
        Coroutine::create(fn () => App::run('Imi\Swoole\Test\Component', SwooleApp::class, static function () use ($channel) {
            PoolManager::use('maindb', function (IPoolResource $resource, IDb $db) {
                $truncateList = [
                    'tb_article',
                    'tb_member',
                    'tb_update_time',
                    'tb_performance',
                ];
                foreach ($truncateList as $table)
                {
                    $db->exec('TRUNCATE ' . $table);
                }
            });
            $channel->push(1);
            $channel->pop();
        }));
        $channel->pop();
    }

    public function executeAfterLastTest(): void
    {
        $this->channel->push(1);
    }
}
