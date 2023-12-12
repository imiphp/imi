<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Server\Cli;

use Imi\App;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Contract\BaseCommand;
use Imi\Event\Event;
use Imi\RoadRunner\HotUpdate\HotUpdateProcess;
use Imi\RoadRunner\Util\RoadRunner;
use Imi\Server\Contract\IServer;
use Imi\Server\Event\ServerEvents;
use Imi\Server\ServerManager;

#[Command(name: 'rr')]
class Server extends BaseCommand
{
    /**
     * 启动 RoadRunner 服务.
     */
    #[CommandAction(name: 'start', description: '启动 RoadRunner 服务')]
    #[Option(name: 'workDir', shortcut: 'w', type: \Imi\Cli\ArgType::STRING, comments: '工作路径')]
    #[Option(name: 'config', shortcut: 'c', type: \Imi\Cli\ArgType::STRING, comments: '配置文件路径，默认 .rr.yaml')]
    public function start(?string $workDir, ?string $config): void
    {
        // 创建服务器对象们前置操作-通用
        Event::dispatch(eventName: ServerEvents::BEFORE_CREATE_SERVERS);
        $server = $this->createServer($workDir, $config);
        // 创建服务器对象们后置操作
        Event::dispatch(eventName: ServerEvents::AFTER_CREATE_SERVERS);
        // 服务器启动前-通用
        Event::dispatch(eventName: ServerEvents::BEFORE_SERVER_START);
        $server->start();
    }

    /**
     * 停止 RoadRunner 服务.
     */
    #[CommandAction(name: 'stop', description: '停止 RoadRunner 服务')]
    #[Option(name: 'workDir', shortcut: 'w', type: \Imi\Cli\ArgType::STRING, comments: '工作路径')]
    #[Option(name: 'config', shortcut: 'c', type: \Imi\Cli\ArgType::STRING, comments: '配置文件路径，默认 .rr.yaml')]
    public function stop(?string $workDir, ?string $config): void
    {
        $server = $this->createServer($workDir, $config);
        $server->shutdown();
    }

    /**
     * 重新加载 RoadRunner 服务.
     */
    #[CommandAction(name: 'reload', description: '重新加载 RoadRunner 服务')]
    #[Option(name: 'workDir', shortcut: 'w', type: \Imi\Cli\ArgType::STRING, comments: '工作路径')]
    #[Option(name: 'config', shortcut: 'c', type: \Imi\Cli\ArgType::STRING, comments: '配置文件路径，默认 .rr.yaml')]
    public function reload(?string $workDir, ?string $config): void
    {
        $server = $this->createServer($workDir, $config);
        $server->reload();
    }

    /**
     * 热更新.
     */
    #[CommandAction(name: 'hotUpdate', description: '热更新')]
    #[Option(name: 'workDir', shortcut: 'w', type: \Imi\Cli\ArgType::STRING, comments: '工作路径')]
    #[Option(name: 'config', shortcut: 'c', type: \Imi\Cli\ArgType::STRING, comments: '配置文件路径，默认 .rr.yaml')]
    public function hotUpdate(?string $workDir, ?string $config): void
    {
        $this->createServer($workDir, $config);
        /** @var HotUpdateProcess $hotUpdate */
        $hotUpdate = App::getBean('hotUpdate');
        $hotUpdate->run();
    }

    private function createServer(?string $workDir, ?string $config): IServer
    {
        return ServerManager::createServer('main', [
            'type'    => 'RoadRunnerHttpServer',
            'workDir' => null === $workDir ? getcwd() : realpath($workDir),
            'config'  => $config,
        ]);
    }
}
