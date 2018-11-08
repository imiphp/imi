<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Util\Imi;
use Imi\Tool\Tool;
use Imi\Main\Helper;
use Imi\Event\EventParam;
use Imi\Util\AtomicManager;
use Imi\Util\ChannelManager;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener(eventName="IMI.INITED",priority=1023)
 */
class Init implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        if('server' === Tool::getToolName() && 'start' === Tool::getToolOperation())
        {
            while(true)
            {
                $result = exec(Imi::getImiCmd('imi', 'buildRuntime'), $output);
                $result = json_decode($result);
                if(true === $result)
                {
                    break;
                }
                else
                {
                    if(null === $result)
                    {
                        echo implode(PHP_EOL, $output), PHP_EOL;
                    }
                    else
                    {
                        echo $result, PHP_EOL;
                    }
                    sleep(1);
                }
            }
            App::loadRuntimeInfo(Imi::getRuntimePath('runtime.cache'));
        }
        App::getBean('ErrorLog')->register();
        foreach(Helper::getMains() as $main)
        {
            $config = $main->getConfig();
            // 原子计数初始化
            AtomicManager::setNames($config['atomics'] ?? []);
            // 通道队列初始化
            ChannelManager::setNames($config['channels'] ?? []);
        }
        AtomicManager::init();
        ChannelManager::init();
    }
}