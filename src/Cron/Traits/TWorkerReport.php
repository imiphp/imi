<?php
namespace Imi\Cron\Traits;

use Imi\App;
use Imi\Log\Log;
use Imi\Cron\Client;
use Imi\Cron\Message\Result;

/**
 * 定时任务上报
 */
trait TWorkerReport
{
    /**
     * 上报定时任务结果
     *
     * @param string $id
     * @param bool $success
     * @param string $message
     * @return void
     */
    protected function reportCronResult(string $id, bool $success, string $message)
    {
        $client = new Client([
            'socketFile'    =>  App::getBean('CronManager')->getSocketFile(),
        ]);
        if($client->connect())
        {
            $result = new Result('CronTask', $id, $success, $message);
            $client->send($result);
            $client->close();
        }
        else
        {
            Log::error('Cannot connect to CronProcess');
        }
    }

}
