<?php

namespace Imi\Cron\Util;

use Imi\App;
use Imi\Cron\Annotation\Cron;
use Imi\Cron\Client;
use Imi\Cron\Message\AddCron;
use Imi\Cron\Message\Clear;
use Imi\Cron\Message\RemoveCron;
use Imi\Cron\Message\Result;
use Imi\Log\Log;

class CronUtil
{
    private function __construct()
    {
    }

    /**
     * 上报定时任务结果.
     *
     * @param string $id
     * @param bool   $success
     * @param string $message
     *
     * @return void
     */
    public static function reportCronResult(string $id, bool $success, string $message)
    {
        $client = new Client([
            'socketFile'    => App::getBean('CronManager')->getSocketFile(),
        ]);
        if ($client->connect())
        {
            $result = new Result('cronTask', $id, $success, $message);
            $client->send($result);
            $client->close();
        }
        else
        {
            Log::error('Cannot connect to CronProcess');
        }
    }

    /**
     * 增加 Cron 任务
     *
     * @param \Imi\Cron\Annotation\Cron $cron
     * @param callable|string           $task
     *
     * @return void
     */
    public static function addCron(Cron $cron, $task)
    {
        $client = new Client([
            'socketFile'    => App::getBean('CronManager')->getSocketFile(),
        ]);
        if ($client->connect())
        {
            $result = new AddCron();
            $result->cronAnnotation = $cron;
            $result->task = $task;
            $client->send($result);
            $client->close();
        }
        else
        {
            Log::error('Cannot connect to CronProcess');
        }
    }

    /**
     * 移除定时任务
     *
     * @param string $id
     *
     * @return void
     */
    public static function removeCron(string $id)
    {
        $client = new Client([
            'socketFile'    => App::getBean('CronManager')->getSocketFile(),
        ]);
        if ($client->connect())
        {
            $result = new RemoveCron();
            $result->id = $id;
            $client->send($result);
            $client->close();
        }
        else
        {
            Log::error('Cannot connect to CronProcess');
        }
    }

    /**
     * 清空定时任务
     *
     * @return void
     */
    public static function clear()
    {
        $client = new Client([
            'socketFile'    => App::getBean('CronManager')->getSocketFile(),
        ]);
        if ($client->connect())
        {
            $result = new Clear();
            $client->send($result);
            $client->close();
        }
        else
        {
            Log::error('Cannot connect to CronProcess');
        }
    }
}
